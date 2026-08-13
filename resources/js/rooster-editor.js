/**
 * RoosterJS — WYSIWYG Editor untuk E-Sign Template Surat
 *
 * Menggantikan CKEditor 5 pada halaman Tambah/Edit Template E-Sign.
 * RoosterJS adalah editor content-model dari Microsoft.
 *
 * Toolbar disusun manual memakai API public dari roosterjs-content-model-api:
 * - Undo / Redo
 * - Heading (H1-H3)
 * - Bold, Italic, Underline, Strikethrough
 * - Font Name, Font Size, Text Color
 * - Alignment (Left, Center, Right, Justify)
 * - Bullet, Numbered List
 * - Indent / Outdent
 * - Block Quote
 * - Insert Table, Link
 * - Clear Format
 *
 * Konsep:
 * - Editor di-render ke <div id="roosterContent"> (dari textarea #templateContent)
 * - Tombol toolbar memanggil API roosterjs-content-model-api
 * - Saat submit, HTML hasil editor disalin ke textarea #templateContent
 */

import { Editor } from 'roosterjs-content-model-core';
import { toggleBold, toggleItalic, toggleUnderline, toggleStrikethrough } from 'roosterjs-content-model-api';
import { toggleSubscript, toggleSuperscript } from 'roosterjs-content-model-api';
import { setTextColor, setBackgroundColor } from 'roosterjs-content-model-api';
import { setFontName, setFontSize, getFormatState } from 'roosterjs-content-model-api';
import { setHeadingLevel } from 'roosterjs-content-model-api';
import { setAlignment } from 'roosterjs-content-model-api';
import { toggleBullet, toggleNumbering } from 'roosterjs-content-model-api';
import { setIndentation } from 'roosterjs-content-model-api';
import { toggleBlockQuote } from 'roosterjs-content-model-api';
import { insertTable, editTable, formatTable, setTableCellShade, applyTableBorderFormat } from 'roosterjs-content-model-api';
import { insertImage } from 'roosterjs-content-model-api';
import { insertLink } from 'roosterjs-content-model-api';
import { clearFormat } from 'roosterjs-content-model-api';
import { undo, redo, createModelFromHtml, exportContent } from 'roosterjs-content-model-core';
import { EditPlugin, PastePlugin, TableEditPlugin } from 'roosterjs-content-model-plugins';
import { mutateBlock } from 'roosterjs-content-model-dom';

const TOOLBAR_ID = 'roosterToolbar';
const EDITOR_ID = 'roosterContent';
const SOURCE_TEXTAREA_ID = 'templateContent';

// ============================================================
// HELPERS
// ============================================================

function qs(selector, scope = document) {
    return scope.querySelector(selector);
}

// ============================================================
// Modal input pengganti prompt() — prompt() diblokir di lingkungan
// sandbox/iframe, sehingga dialog dibuat memakai Bootstrap modal.
// ============================================================
function showInputModal(opts) {
    // opts: { title, fields:[{label, value}], submitLabel, onSubmit(values) }
    const id = 'roosterInputModal';
    let modalEl = document.getElementById(id);
    if (modalEl) modalEl.remove();

    const fields = opts.fields.map((f, i) =>
        '<div class="mb-3">' +
            '<label class="form-label" style="font-size:12px;font-weight:600;">' + f.label + '</label>' +
            '<input type="' + (f.type || 'number') + '" class="form-control"' +
                (f.type !== 'number' ? '' : ' min="1" max="20"') +
                ' value="' + (f.value ?? '') + '" data-field="' + i + '">' +
        '</div>'
    ).join('');

    modalEl = document.createElement('div');
    modalEl.className = 'modal fade';
    modalEl.id = id;
    modalEl.tabIndex = '-1';
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.innerHTML =
        '<div class="modal-dialog modal-sm modal-dialog-centered">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<h6 class="modal-title" style="font-size:14px;">' + opts.title + '</h6>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                '</div>' +
                '<div class="modal-body">' + fields + '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>' +
                    '<button type="button" class="btn btn-primary" id="roosterInputOk">' + (opts.submitLabel || 'OK') + '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    document.body.appendChild(modalEl);

    const modal = new window.bootstrap.Modal(modalEl);
    modal.show();

    modalEl.querySelector('#roosterInputOk').addEventListener('click', () => {
        const rawValues = Array.from(modalEl.querySelectorAll('[data-field]')).map((el) => el.value || '');
        const values = rawValues.map((v, i) => {
            const f = opts.fields[i];
            return f.type !== 'number' ? v : (parseInt(v, 10) || 1);
        });
        const editable = document.getElementById(EDITOR_ID);
        if (editable) editable.focus();
        modal.hide();
        opts.onSubmit(values);
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        const editable = document.getElementById(EDITOR_ID);
        if (editable) editable.focus();
        modalEl.remove();
    });
    setTimeout(() => {
        const first = modalEl.querySelector('[data-field]');
        if (first) first.focus();
    }, 200);
}

// ============================================================
// Modal format tabel — mirip menu "Table Properties" di Word.
// Menyesuaikan: posisi tabel, warna border, header row, banded,
// shade sel, dan vertical align konten.
// ============================================================
function showTableFormatModal(editor) {
    const id = 'roosterTableFormatModal';
    let modalEl = document.getElementById(id);
    if (modalEl) modalEl.remove();

    modalEl = document.createElement('div');
    modalEl.className = 'modal fade';
    modalEl.id = id;
    modalEl.tabIndex = '-1';
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.innerHTML =
        '<div class="modal-dialog modal-lg modal-dialog-centered">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
                    '<h6 class="modal-title" style="font-size:14px;">Format Tabel</h6>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                '</div>' +
                '<div class="modal-body">' +
                    '<div class="row g-3">' +
                        '<div class="col-md-6">' +
                            '<label class="form-label" style="font-size:12px;font-weight:600;">Posisi Tabel</label>' +
                            '<div class="d-flex gap-2" id="tf-align">' +
                                '<button type="button" class="btn btn-outline-primary btn-sm flex-fill" data-align="left">Kiri</button>' +
                                '<button type="button" class="btn btn-outline-primary btn-sm flex-fill" data-align="center">Tengah</button>' +
                                '<button type="button" class="btn btn-outline-primary btn-sm flex-fill" data-align="right">Kanan</button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-6">' +
                            '<label class="form-label" style="font-size:12px;font-weight:600;">Vertical Align Konten</label>' +
                            '<div class="d-flex gap-2" id="tf-valign">' +
                                '<button type="button" class="btn btn-outline-primary btn-sm flex-fill" data-valign="top">Atas</button>' +
                                '<button type="button" class="btn btn-outline-primary btn-sm flex-fill" data-valign="middle">Tengah</button>' +
                                '<button type="button" class="btn btn-outline-primary btn-sm flex-fill" data-valign="bottom">Bawah</button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-4">' +
                            '<label class="form-label" style="font-size:12px;font-weight:600;">Warna Border</label>' +
                            '<input type="color" class="form-control form-control-color" id="tf-bordercolor" value="#212529">' +
                        '</div>' +
                        '<div class="col-md-4">' +
                            '<label class="form-label" style="font-size:12px;font-weight:600;">Warna Shade Sel</label>' +
                            '<input type="color" class="form-control form-control-color" id="tf-shade" value="#ffffff">' +
                        '</div>' +
                        '<div class="col-md-4 d-flex align-items-end">' +
                            '<div class="form-check">' +
                                '<input class="form-check-input" type="checkbox" id="tf-header">' +
                                '<label class="form-check-label" for="tf-header" style="font-size:12px;">Header Row</label>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>' +
                    '<button type="button" class="btn btn-primary" id="tf-apply">Terapkan</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    document.body.appendChild(modalEl);

    const modal = new window.bootstrap.Modal(modalEl);
    modal.show();

    let align = null, valign = null;
    modalEl.querySelectorAll('#tf-align [data-align]').forEach((b) => {
        b.addEventListener('click', () => {
            modalEl.querySelectorAll('#tf-align [data-align]').forEach((x) => x.classList.remove('btn-primary'));
            modalEl.querySelectorAll('#tf-align [data-align]').forEach((x) => x.classList.add('btn-outline-primary'));
            b.classList.remove('btn-outline-primary'); b.classList.add('btn-primary');
            align = b.dataset.align;
        });
    });
    modalEl.querySelectorAll('#tf-valign [data-valign]').forEach((b) => {
        b.addEventListener('click', () => {
            modalEl.querySelectorAll('#tf-valign [data-valign]').forEach((x) => x.classList.remove('btn-primary'));
            modalEl.querySelectorAll('#tf-valign [data-valign]').forEach((x) => x.classList.add('btn-outline-primary'));
            b.classList.remove('btn-outline-primary'); b.classList.add('btn-primary');
            valign = b.dataset.valign;
        });
    });

    modalEl.querySelector('#tf-apply').addEventListener('click', () => {
        const borderColor = modalEl.querySelector('#tf-bordercolor').value;
        const shade = modalEl.querySelector('#tf-shade').value;
        const header = modalEl.querySelector('#tf-header').checked;

        if (align) {
            editTable(editor, align === 'left' ? 'alignLeft' : align === 'center' ? 'alignCenter' : 'alignRight');
        }
        if (valign) {
            editTable(editor, valign === 'top' ? 'alignCellTop' : valign === 'middle' ? 'alignCellMiddle' : 'alignCellBottom');
        }
        if (header) {
            formatTable(editor, { hasHeaderRow: true });
        }
        // Border warna
        applyTableBorderFormat(editor, { color: borderColor, width: '1px', style: 'solid' }, 'allBorders');
        // Shade sel saat ini (jika bukan putih/hapus)
        if (shade && shade !== '#ffffff') {
            setTableCellShade(editor, shade);
        }

        // Pindahkan fokus ke editor sebelum menutup modal (hindari aria-hidden warning)
        const editable = document.getElementById(EDITOR_ID);
        if (editable) editable.focus();
        modal.hide();
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        const editable = document.getElementById(EDITOR_ID);
        if (editable) editable.focus();
        modalEl.remove();
    });
}

function buildToolbarButton(editor, label, icon, action) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'rooster-tool-btn';
    btn.title = label;
    btn.innerHTML = icon;
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        action(editor);
    });
    return btn;
}

function buildDropdown(editor, label, options, apply, role) {
    const wrap = document.createElement('span');
    wrap.className = 'rooster-tool-dropdown';

    const select = document.createElement('select');
    select.className = 'rooster-tool-select';
    if (role) select.dataset.role = role;
    options.forEach((opt) => {
        const o = document.createElement('option');
        o.value = opt.value;
        o.textContent = opt.label;
        select.appendChild(o);
    });
    select.addEventListener('change', () => {
        apply(editor, select.value);
        select.selectedIndex = 0;
        updateFormatIndicators(editor);
    });

    wrap.appendChild(select);
    return wrap;
}

function buildColorButton(editor, label, icon, applyColor) {
    const wrap = document.createElement('span');
    wrap.className = 'rooster-tool-dropdown';

    const input = document.createElement('input');
    input.type = 'color';
    input.className = 'rooster-tool-color';
    input.title = label;
    input.value = '#000000';
    input.addEventListener('input', () => {
        applyColor(editor, input.value);
    });

    wrap.appendChild(input);
    return wrap;
}

function buildImageButton(editor, label, icon) {
    const wrap = document.createElement('span');
    wrap.className = 'rooster-tool-dropdown';

    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.className = 'rooster-tool-file';
    input.title = label;
    input.style.display = 'none';

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'rooster-tool-btn';
    btn.innerHTML = icon;
    btn.title = label;
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        input.click();
    });

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (file) {
            // Hapus pemilihan agar bisa pilih ulang
            const imgUrl = URL.createObjectURL(file);
            insertImage(editor, imgUrl);
            input.value = '';
            // Revoke setelah 1 detik supaya blob tetap tampil
            setTimeout(() => URL.revokeObjectURL(imgUrl), 1000);
        }
    });

    wrap.appendChild(btn);
    wrap.appendChild(input);
    return wrap;
}

// ============================================================
// TOOLBAR BUILDER
// ============================================================

function createToolbar(editor) {
    const toolbar = document.createElement('div');
    toolbar.id = TOOLBAR_ID;
    toolbar.className = 'rooster-toolbar';
    toolbar.dataset.active = '';

    const icons = {
        bold: '<b style="font-weight:900">B</b>',
        italic: '<i style="font-style:italic">I</i>',
        underline: '<u style="text-decoration:underline">U</u>',
        strike: '<s style="text-decoration:line-through">S</s>',
        subscript: '<sub style="font-size:11px">x<sub style="font-size:9px">2</sub></sub>',
        superscript: '<sup style="font-size:11px">x<sup style="font-size:9px">2</sup></sup>',
        fontColor: '<span style="border-bottom:3px solid #f00;font-weight:600">A</span>',
        bgColor: '<span style="background:#ff0;font-weight:600">A</span>',
        alignLeft: '⇤',
        alignCenter: '⇔',
        alignRight: '⇥',
        justify: '☰',
        bullet: '•≡',
        number: '1≡',
        indent: '→',
        outdent: '←',
        quote: '❝',
        table: '▦',
        tableRow: '≡',
        tableCol: '≣',
        tableMerge: '⊞',
        tableSplit: '⧉',
        tableDelete: '🗑',
        tableAlign: '⇥',
        tableCell: '▤',
        tableShade: '▨',
        tableBorder: '▦',
        image: '🖼',
        link: '🔗',
        undo: '↶',
        redo: '↷',
        clear: '✖',
    };

    // ---- Menu definitions: each menu has a name + builder function for its buttons ----
    const menuDefs = [
        {
            key: 'main',
            name: 'Main',
            build: (p) => {
                p.appendChild(buildToolbarButton(editor, 'Undo', icons.undo, (e) => undo(e)));
                p.appendChild(buildToolbarButton(editor, 'Redo', icons.redo, (e) => redo(e)));
                p.appendChild(sep());
                p.appendChild(buildDropdown(editor, 'Heading', [
                    { value: '0', label: 'Normal' },
                    { value: '1', label: 'Heading 1' },
                    { value: '2', label: 'Heading 2' },
                    { value: '3', label: 'Heading 3' },
                    { value: '4', label: 'Heading 4' },
                    { value: '5', label: 'Heading 5' },
                    { value: '6', label: 'Heading 6' },
                ], (ed, v) => setHeadingLevel(ed, parseInt(v, 10))));
                p.appendChild(sep());
                p.appendChild(buildColorButton(editor, 'Text Color', icons.fontColor, (ed, c) => setTextColor(ed, c)));
                p.appendChild(buildColorButton(editor, 'Background Color', icons.bgColor, (ed, c) => setBackgroundColor(ed, c)));
                p.appendChild(sep());
                p.appendChild(buildToolbarButton(editor, 'Clear Format', icons.clear, (ed) => clearFormat(ed)));
            },
        },
        {
            key: 'text',
            name: 'Text',
            build: (p) => {
                p.appendChild(buildToolbarButton(editor, 'Bold', icons.bold, (e) => toggleBold(e)));
                p.appendChild(buildToolbarButton(editor, 'Italic', icons.italic, (e) => toggleItalic(e)));
                p.appendChild(buildToolbarButton(editor, 'Underline', icons.underline, (e) => toggleUnderline(e)));
                p.appendChild(buildToolbarButton(editor, 'Strikethrough', icons.strike, (e) => toggleStrikethrough(e)));
                p.appendChild(buildToolbarButton(editor, 'Subscript', icons.subscript, (e) => toggleSubscript(e)));
                p.appendChild(buildToolbarButton(editor, 'Superscript', icons.superscript, (e) => toggleSuperscript(e)));
                p.appendChild(sep());
                p.appendChild(buildDropdown(editor, 'Font', [
                    { value: 'Calibri', label: 'Calibri' },
                    { value: 'Arial', label: 'Arial' },
                    { value: 'Times New Roman', label: 'Times New Roman' },
                    { value: 'Courier New', label: 'Courier New' },
                    { value: 'Tahoma', label: 'Tahoma' },
                    { value: 'Verdana', label: 'Verdana' },
                    { value: 'Georgia', label: 'Georgia' },
                    { value: 'Trebuchet MS', label: 'Trebuchet MS' },
                ], (ed, v) => setFontName(ed, v), 'font'));
                p.appendChild(buildDropdown(editor, 'Font Size', [
                    { value: '8pt', label: '8' }, { value: '9pt', label: '9' }, { value: '10pt', label: '10' },
                    { value: '11pt', label: '11' }, { value: '12pt', label: '12' }, { value: '14pt', label: '14' },
                    { value: '16pt', label: '16' }, { value: '18pt', label: '18' }, { value: '20pt', label: '20' },
                    { value: '24pt', label: '24' }, { value: '28pt', label: '28' }, { value: '36pt', label: '36' },
                    { value: '48pt', label: '48' }, { value: '72pt', label: '72' },
                ], (ed, v) => setFontSize(ed, v), 'fontsize'));
            },
        },
        {
            key: 'paragraph',
            name: 'Paragraph',
            build: (p) => {
                p.appendChild(buildToolbarButton(editor, 'Align Left', icons.alignLeft, (e) => setAlignment(e, 'left')));
                p.appendChild(buildToolbarButton(editor, 'Align Center', icons.alignCenter, (e) => setAlignment(e, 'center')));
                p.appendChild(buildToolbarButton(editor, 'Align Right', icons.alignRight, (e) => setAlignment(e, 'right')));
                p.appendChild(buildToolbarButton(editor, 'Justify', icons.justify, (e) => setAlignment(e, 'justify')));
                p.appendChild(sep());
                p.appendChild(buildToolbarButton(editor, 'Bullet List', icons.bullet, (e) => toggleBullet(e)));
                p.appendChild(buildToolbarButton(editor, 'Numbered List', icons.number, (e) => toggleNumbering(e)));
                p.appendChild(buildToolbarButton(editor, 'Indent', icons.indent, (e) => setIndentation(e, 'indent')));
                p.appendChild(buildToolbarButton(editor, 'Outdent', icons.outdent, (e) => setIndentation(e, 'outdent')));
                p.appendChild(sep());
                p.appendChild(buildToolbarButton(editor, 'Block Quote', icons.quote, (e) => toggleBlockQuote(e)));
            },
        },
        {
            key: 'insert',
            name: 'Insert',
            build: (p) => {
                p.appendChild(buildToolbarButton(editor, 'Insert Link', icons.link, (e) => {
                    showInputModal({
                        title: 'Insert Link',
                        fields: [{ label: 'URL Link', value: 'https://', type: 'text' }],
                        submitLabel: 'Sisipkan',
                        onSubmit: (vals) => {
                            const url = vals[0];
                            if (url) { insertLink(e, url); }
                        },
                    });
                }));
                p.appendChild(buildToolbarButton(editor, 'Page Layout', '▭', (e) => {
                    const modalEl = document.getElementById('pageLayoutModal');
                    if (modalEl && window.bootstrap) {
                        const modal = new window.bootstrap.Modal(modalEl);
                        modal.show();
                    }
                }));
            },
        },
        {
            key: 'table',
            name: 'Table',
            build: (p) => {
                // Sisip tabel baru
                p.appendChild(buildToolbarButton(editor, 'Insert Table', icons.table, (e) => {
                    showInputModal({
                        title: 'Insert Tabel',
                        fields: [
                            { label: 'Jumlah Kolom', value: 3 },
                            { label: 'Jumlah Baris', value: 2 },
                        ],
                        submitLabel: 'Sisipkan',
                        onSubmit: (vals) => {
                            const c = vals[0], r = vals[1];
                            if (c > 0 && r > 0) { insertTable(e, c, r); }
                        },
                    });
                }));
                p.appendChild(sep());

                // Sisip baris / kolom
                p.appendChild(buildToolbarButton(editor, 'Insert Row Above', '↑' + icons.tableRow, (e) => editTable(e, 'insertAbove')));
                p.appendChild(buildToolbarButton(editor, 'Insert Row Below', '↓' + icons.tableRow, (e) => editTable(e, 'insertBelow')));
                p.appendChild(buildToolbarButton(editor, 'Insert Column Left', '←' + icons.tableCol, (e) => editTable(e, 'insertLeft')));
                p.appendChild(buildToolbarButton(editor, 'Insert Column Right', '→' + icons.tableCol, (e) => editTable(e, 'insertRight')));
                p.appendChild(sep());

                // Gabung / pisah sel
                p.appendChild(buildToolbarButton(editor, 'Merge Cells', icons.tableMerge, (e) => editTable(e, 'mergeCells')));
                p.appendChild(buildToolbarButton(editor, 'Split Cell', icons.tableSplit, (e) => editTable(e, 'splitVertically')));
                p.appendChild(sep());

                // Hapus
                p.appendChild(buildToolbarButton(editor, 'Delete Row', icons.tableRow + '✕', (e) => editTable(e, 'deleteRow')));
                p.appendChild(buildToolbarButton(editor, 'Delete Column', icons.tableCol + '✕', (e) => editTable(e, 'deleteColumn')));
                p.appendChild(buildToolbarButton(editor, 'Delete Table', icons.tableDelete, (e) => editTable(e, 'deleteTable')));
                p.appendChild(sep());

                // Posisi tabel
                p.appendChild(buildToolbarButton(editor, 'Align Table Left', '⇤', (e) => editTable(e, 'alignLeft')));
                p.appendChild(buildToolbarButton(editor, 'Align Table Center', '⇔', (e) => editTable(e, 'alignCenter')));
                p.appendChild(buildToolbarButton(editor, 'Align Table Right', '⇥', (e) => editTable(e, 'alignRight')));
                p.appendChild(sep());

                // Format tabel (modal properties)
                p.appendChild(buildToolbarButton(editor, 'Table Properties', icons.tableBorder, (e) => showTableFormatModal(e)));
            },
        },
        {
            key: 'image',
            name: 'Image',
            build: (p) => {
                p.appendChild(buildImageButton(editor, 'Insert Image', icons.image));
            },
        },
    ];

    // ---- Build menubar (tabs) ----
    const menubar = document.createElement('div');
    menubar.className = 'rooster-menubar';

    menuDefs.forEach((menu) => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'rooster-menu-item';
        item.textContent = menu.name;
        item.dataset.menu = menu.key;
        item.addEventListener('click', (e) => {
            e.preventDefault();
            activateMenu(toolbar, menu.key, editor, menu.build);
        });
        menubar.appendChild(item);
    });

    // ---- Panel untuk menampung tombol menu aktif ----
    const panel = document.createElement('div');
    panel.className = 'rooster-tool-panel';

    toolbar.appendChild(menubar);
    toolbar.appendChild(panel);

    // Aktifkan menu pertama (Main) sebagai default
    activateMenu(toolbar, 'main', editor, menuDefs[0].build);

    return toolbar;
}

function buildMenu(editor, key, name, builder) {
    const wrap = document.createElement('div');
    wrap.className = 'rooster-menu';
    wrap.dataset.menu = key;

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'rooster-menu-item';
    btn.textContent = name;

    const panel = document.createElement('div');
    panel.className = 'rooster-menu-panel';
    builder(panel);

    wrap.appendChild(btn);
    wrap.appendChild(panel);
    return wrap;
}

let activeMenuKey = null;

function activateMenu(toolbar, key, editor, builder) {
    const panel = toolbar.querySelector('.rooster-tool-panel');
    if (!panel) return;

    // Toggle: klik menu yang sama menutup panel
    if (activeMenuKey === key) {
        activeMenuKey = null;
        panel.innerHTML = '';
        toolbar.dataset.active = '';
        toolbar.querySelectorAll('.rooster-menu-item').forEach((mi) => mi.classList.remove('active'));
        return;
    }

    activeMenuKey = key;
    panel.innerHTML = '';
    const inner = document.createElement('div');
    inner.className = 'rooster-tool-panel-inner';
    builder(inner);
    panel.appendChild(inner);

    toolbar.dataset.active = key;
    toolbar.querySelectorAll('.rooster-menu-item').forEach((mi) => {
        mi.classList.toggle('active', mi.dataset.menu === key);
    });
}

function sep() {
    const s = document.createElement('span');
    s.className = 'rooster-tool-sep';
    return s;
}

// ============================================================
// FORMAT INDICATORS (Font / Font Size ikut seleksi teks)
// ============================================================

function normalizeFontName(name) {
    if (!name) return '';
    let str = String(name).trim();
    // Buang tanda kutip (mis. '"Arial"') dan spasi-normalisasi
    str = str.replace(/['"]/g, ' ').replace(/\s+/g, ' ').trim();
    // "Arial, sans-serif" => ambil family pertama (nilai si ternormalisasi tetap 'Arial')
    return str.split(',')[0].trim();
}

function updateFormatIndicators(editor) {
    if (!editor) return;
    try {
        const state = getFormatState(editor);
        const activeMenu = document.querySelector('#roosterToolbar .rooster-menu-item.active');
        if (!activeMenu || activeMenu.dataset.menu !== 'text') return;

        // Font Name
        const fontSelect = activeMenu.closest('#roosterToolbar')
            .querySelector('select[data-role="font"]');
        if (fontSelect) {
            const current = normalizeFontName(state.fontName);
            const match = Array.prototype.find.call(fontSelect.options, (o) =>
                normalizeFontName(o.value).toLowerCase() === current.toLowerCase()
            );
            if (match) fontSelect.value = match.value;
            else fontSelect.selectedIndex = 0;
        }

        // Font Size
        const sizeSelect = activeMenu.closest('#roosterToolbar')
            .querySelector('select[data-role="fontsize"]');
        if (sizeSelect) {
            const currentSize = String(state.fontSize || '').trim();
            const match = Array.prototype.find.call(sizeSelect.options, (o) =>
                o.value.trim().toLowerCase() === currentSize.toLowerCase()
            );
            if (match) sizeSelect.value = match.value;
            else sizeSelect.selectedIndex = 0;
        }
    } catch (err) {
        // abaikan jika menu Text belum terbuka / belum ada seleksi
    }
}

// ============================================================
// SET CONTENT (HTML -> editor)
// ============================================================

function setEditorContent(editor, html) {
    if (!html) {
        return;
    }
    try {
        const model = createModelFromHtml(html);
        if (model) {
            editor.formatContentModel((m) => {
                // Replace all blocks with the new model's blocks
                (m.blocks || []).splice(0, m.blocks.length, ...model.blocks);
                return true;
            });
        }
    } catch (err) {
        console.error('RoosterJS setContent error:', err);
        editor.getDOMHelper()?.root?.appendChild?.(
            document.createTextNode(html)
        );
    }
}

function getEditorContent(editor) {
    // Placeholder tanda tangan bersifat visual saja — dikeluarkan sebelum export
    // agar tidak ikut tersimpan ke konten (area sign ditambahkan otomatis saat surat jadi).
    stripSignaturePlaceholder();
    let html;
    try {
        html = exportContent(editor, 'HTML');
    } catch (err) {
        console.error('RoosterJS getContent error:', err);
        html = '';
    }
    appendSignaturePlaceholder();
    return html;
}

// ============================================================
// SIGNATURE PLACEHOLDER (visual, non-editable)
// Ditampilkan sebagai blok terakhir di kertas A4 editor sebagai gambaran
// lokasi tanda tangan. Tidak tersimpan ke konten.
// ============================================================

function getSignaturePlaceholder() {
    return document.getElementById('editor-sign-placeholder');
}

function getActiveSigns() {
    const signs = [];
    if (document.getElementById('inputSign1')?.value === '1') signs.push(1);
    if (document.getElementById('inputSign2')?.value === '1') signs.push(2);
    if (document.getElementById('inputSign3')?.value === '1') signs.push(3);
    return signs;
}

function buildSignatureBoxHtml(label) {
    return '<div class="editor-sign-box">' +
        '<div class="editor-sign-box-label">' + label + '</div>' +
        '<div class="editor-sign-box-qr">QR Code<br>Digital Signature</div>' +
        '</div>';
}

function appendSignaturePlaceholder() {
    const editable = document.getElementById(EDITOR_ID);
    if (!editable || getSignaturePlaceholder()) return;
    const active = getActiveSigns();
    // Posisi tetap: kiri=Sign2, tengah=Sign3, kanan=Sign1
    const slotLabels = {
        left: active.includes(2) ? 'Sign 2' : null,
        center: active.includes(3) ? 'Sign 3' : null,
        right: active.includes(1) ? 'Sign 1' : null,
    };
    const ph = document.createElement('div');
    ph.id = 'editor-sign-placeholder';
    ph.contentEditable = 'false';
    ph.className = 'editor-sign-placeholder';
    ph.innerHTML =
        '<div class="editor-sign-slot">' + (slotLabels.left ? buildSignatureBoxHtml(slotLabels.left) : '') + '</div>' +
        '<div class="editor-sign-slot">' + (slotLabels.center ? buildSignatureBoxHtml(slotLabels.center) : '') + '</div>' +
        '<div class="editor-sign-slot">' + (slotLabels.right ? buildSignatureBoxHtml(slotLabels.right) : '') + '</div>';
    editable.appendChild(ph);
}

function stripSignaturePlaceholder() {
    const ph = getSignaturePlaceholder();
    if (ph && ph.parentNode) ph.parentNode.removeChild(ph);
}

window.refreshEditorSignPlaceholder = function () {
    stripSignaturePlaceholder();
    appendSignaturePlaceholder();
};

// ============================================================
// INIT
// ============================================================

function injectStyles() {
    const styleId = 'rooster-editor-styles';
    if (document.getElementById(styleId)) return;
    const style = document.createElement('style');
    style.id = styleId;
    style.textContent = `
        .rooster-toolbar {
            border: 1px solid #dee2e6;
            border-radius: 6px 6px 0 0;
            background: #f8f9fa;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .rooster-menubar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            padding: 6px 8px;
            border-bottom: 1px solid #dee2e6;
            background: #f1f3f5;
        }
        .rooster-menu-item {
            min-width: 58px;
            padding: 6px 14px;
            border: 1px solid transparent;
            border-radius: 4px;
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            cursor: pointer;
        }
        .rooster-menu-item:hover {
            background: #e9ecef;
        }
        .rooster-menu-item.active {
            background: #fff;
            border-color: #405189;
            color: #405189;
        }
        .rooster-tool-panel {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            background: #fff;
            min-height: 40px;
        }
        .rooster-tool-panel-inner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
        }
        .rooster-tool-btn {
            min-width: 30px;
            height: 30px;
            padding: 0 6px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #fff;
            font-size: 13px;
            color: #212529;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .rooster-tool-btn:hover {
            background: #e9ecef;
            border-color: #405189;
        }
        .rooster-tool-sep {
            width: 1px;
            height: 22px;
            background: #ced4da;
            margin: 0 4px;
            display: inline-block;
        }
        .rooster-tool-select {
            height: 30px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #fff;
            font-size: 12px;
            color: #212529;
            cursor: pointer;
            padding: 0 4px;
        }
        .rooster-tool-dropdown {
            display: inline-flex;
            align-items: center;
        }
        .rooster-tool-color {
            width: 28px;
            height: 28px;
            padding: 0;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #fff;
            cursor: pointer;
        }
        .rooster-tool-file {
            display: none;
        }
        .rooster-editable {
            min-height: 500px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 6px 6px;
            padding: 16px;
            background: #fff;
            font-family: Calibri, Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            outline: none;
        }
        /* Garis pembatas tegas antara panel toolbar dan konten */
        .rooster-tool-panel {
            border-bottom: 2px solid #405189;
        }
        /* Table selalu tampil dengan border yang jelas */
        .rooster-editable table {
            border-collapse: collapse !important;
            width: 100%;
        }
        .rooster-editable table td,
        .rooster-editable table th {
            border: 1px solid #495057 !important;
            padding: 6px 8px;
            vertical-align: top;
        }
        .rooster-editable table th {
            background: #f0f0f0;
            font-weight: 700;
        }
        .editor-sign-placeholder {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            width: 100%;
            page-break-inside: avoid;
        }
        .editor-sign-slot {
            display: flex;
            justify-content: center;
        }
        .editor-sign-box {
            width: 170px;
            padding: 10px;
            border: 1px dashed #adb5bd;
            border-radius: 8px;
            text-align: center;
            background: #ffffff;
        }
        .editor-sign-box-label {
            font-weight: 700;
            font-size: 11pt;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .editor-sign-box-qr {
            width: 110px;
            height: 70px;
            border: 2px dashed #adb5bd;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
            font-size: 9px;
            color: #adb5bd;
            background: #ffffff;
        }
    `;
    document.head.appendChild(style);
}

// ============================================================
// Resize tepi luar tabel (paling kiri / paling kanan).
// Berbeda dari perilaku bawaan CellResizer yang hanya menggeser
// garis dalam, di sini seluruh lebar tabel ikut melebar/mengecil
// secara proporsional (semua kolom diskalakan), seperti di Word.
//
// Diterapkan pada level document (capture phase) supaya menang
// lebih dulu dari handle bawaan plugin yang diletakkan tepat di
// tepi tabel. Posisi dicek secara geometris terhadap semua tabel
// di dalam editor, bukan dari event.target (yang bisa jadi handle).
// ============================================================
function enableTableOuterEdgeResize(editor, editable) {
    const EDGE_ZONE = 6; // piksel di tepi tabel yang dianggap zona resize

    let drag = null;

    // Semua tabel di dalam editor (termasuk handle plugin yang posisinya mengikuti tabel)
    function tables() {
        return Array.prototype.slice.call(editable.querySelectorAll('table'));
    }

    // Tentukan sisi (left/right) jika titik mouse dekat tepi tabel
    function detectEdgeAt(x, y) {
        for (const table of tables()) {
            const rect = table.getBoundingClientRect();
            if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) continue;
            if (Math.abs(x - rect.left) <= EDGE_ZONE) return { table, side: 'left' };
            if (Math.abs(x - rect.right) <= EDGE_ZONE) return { table, side: 'right' };
        }
        return null;
    }

    // Ubah cursor ketika di atas tepi kiri/kanan tabel
    document.addEventListener('mousemove', (e) => {
        if (drag) return;
        const inside = e.target === editable || (e.target instanceof Node && editable.contains(e.target));
        const edge = inside ? detectEdgeAt(e.clientX, e.clientY) : null;
        editable.style.cursor = edge ? 'col-resize' : '';
    }, true);

    document.addEventListener('mousedown', (e) => {
        const inside = e.target === editable || (e.target instanceof Node && editable.contains(e.target));
        if (!inside || e.button !== 0) return;

        const edge = detectEdgeAt(e.clientX, e.clientY);
        if (!edge) return;

        // Mencegat sebelum plugin menjalankan resize kolom dalam
        e.preventDefault();
        e.stopPropagation();

        const { table, side } = edge;
        const rect = table.getBoundingClientRect();
        const startX = e.clientX;
        const startW = rect.width;
        const visible = table.tBodies.length ? table.tBodies[0] : table;

        drag = { table, side, startX, startW, widths: [] };

        // Simpan lebar awal tiap kolom (dari baris pertama)
        const firstRow = visible.rows[0];
        if (firstRow) {
            for (const cell of firstRow.cells) {
                drag.widths.push(cell.getBoundingClientRect().width);
            }
        }

        const onMove = (ev) => {
            if (!drag) return;
            const delta = side === 'right' ? ev.clientX - startX : startX - ev.clientX;
            const ratio = (startW + delta) / startW;
            const newWidths = drag.widths.map((w) => Math.max(30, w * ratio));
            scaleTableColumns(editor, table, newWidths);
        };

        const onUp = () => {
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
            drag = null;
            editable.style.cursor = '';
        };

        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
    }, true);

    // Tulis lebar kolom baru ke content model (agar tersimpan saat export)
    function scaleTableColumns(editorInstance, table, widths) {
        editorInstance.formatContentModel((model) => {
            let found = false;
            (function walk(blocks) {
                for (const block of blocks) {
                    if (block.blockType === 'Table') {
                        if (block.cachedElement === table) {
                            const t = mutateBlock(block);
                            widths.forEach((w, i) => {
                                if (i < t.widths.length) t.widths[i] = w;
                            });
                            found = true;
                            return;
                        }
                    } else if (block.blocks) {
                        walk(block.blocks);
                    } else if (block.cells) {
                        for (const cell of block.cells) walk(cell.blocks || []);
                    }
                }
            })(model.blocks);
            return found;
        }, { skipDOMSelection: true });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const source = document.getElementById(SOURCE_TEXTAREA_ID);
    if (!source) return;

    injectStyles();

    // Build the container structure
    source.style.display = 'none';

    const wrapper = document.createElement('div');
    wrapper.className = 'rooster-wrapper';

    // Create editable div
    const editable = document.createElement('div');
    editable.id = EDITOR_ID;
    editable.className = 'rooster-editable';
    editable.setAttribute('contenteditable', 'true');

    source.parentNode.insertBefore(wrapper, source.nextSibling);
    wrapper.appendChild(editable);

    // Plugins
    const editPlugin = new EditPlugin();
    const pastePlugin = new PastePlugin();
    // TableEditPlugin: menyediakan drag-resize kolom/baris, pemilih kolom/baris,
    // dan memindah tabel — persis seperti di Microsoft Word.
    const tableEditPlugin = new TableEditPlugin();

    const editor = new Editor(editable, {
        plugins: [editPlugin, pastePlugin, tableEditPlugin],
        defaultSegmentFormat: {
            fontFamily: 'Calibri',
            fontSize: '12pt',
        },
    });

    const toolbar = createToolbar(editor);
    wrapper.insertBefore(toolbar, editable);

    // Aktifkan resize tepi luar tabel (kiri/kanan) agar melebar/mengecil seluruh tabel
    enableTableOuterEdgeResize(editor, editable);

    // Set initial content from textarea
    setEditorContent(editor, source.value || '');

    // Placeholder tanda tangan (visual) sebagai blok terakhir di kertas A4
    appendSignaturePlaceholder();
    // Pastikan placeholder tetap ada meski RoosterJS merender ulang konten
    const phObserver = new MutationObserver(() => {
        if (!editable.contains(getSignaturePlaceholder())) {
            appendSignaturePlaceholder();
        }
    });
    phObserver.observe(editable, { childList: true, subtree: true });

    // Terapkan margin/layout dari hidden fields (halaman edit template)
    setTimeout(() => {
        try {
            const top = document.getElementById('inputMarginTop')?.value || 25;
            const bottom = document.getElementById('inputMarginBottom')?.value || 25;
            const left = document.getElementById('inputMarginLeft')?.value || 25;
            const right = document.getElementById('inputMarginRight')?.value || 25;
            editable.style.padding = top + 'mm ' + right + 'mm ' + bottom + 'mm ' + left + 'mm';
        } catch (err) {
            // ignore
        }
    }, 100);

    // Keep instance for later use
    window.roosterEditor = editor;
    // Helper untuk membaca konten editor (dipakai preview & submit)
    window.getRoosterContent = () => getEditorContent(editor);

    // Perbarui indikator Font / Font Size mengikuti posisi kursor
    const updateSelection = () => {
        if (editor.getDOMSelection && editor.getDOMSelection()) {
            updateFormatIndicators(editor);
        }
    };
    document.addEventListener('selectionchange', updateSelection);
    // jejak seleksi saat awal (mis. font default) tanpa perlu teks dipilih
    window.addEventListener('load', () => setTimeout(updateSelection, 150));

    // Expose insert placeholder (dipakai oleh panel Placeholder E-Sign)
    window.insertPlaceholder = (placeholderText) => {
        editable.focus();
        // Insert text at cursor caret position via native execCommand
        document.execCommand('insertText', false, placeholderText);
    };

    // Sync back to textarea on submit
    const form = source.closest('form');
    if (form) {
        form.addEventListener('submit', () => {
            source.value = getEditorContent(editor);
        });
    }
});
