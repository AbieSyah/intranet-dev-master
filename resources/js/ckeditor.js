/**
 * CKEditor 5 — Custom bundle untuk E-Sign Template Surat
 *
 * Toolbar dirancang semirip Microsoft Word / Google Docs untuk
 * pembuatan template surat kantor.
 *
 * Plugin aktif:
 * - Essentials (Undo/Redo, Enter, Typing)
 * - PasteFromOffice — paste dari Word tetap rapi
 * - Heading (H1-H6)
 * - Font (FontFamily, FontSize, FontColor, FontBackgroundColor)
 * - Bold, Italic, Underline, Strikethrough
 * - Highlight, Alignment
 * - LineHeight (Line spacing seperti Word: 1.0, 1.15, 1.5, 2.0, dll)
 * - List (Bulleted, Numbered), TodoList (Checklist)
 * - Indent, Outdent, BlockQuote
 * - HorizontalLine, Link
 * - Table (dengan TableToolbar, TableCellProperties, TableProperties)
 * - RemoveFormat
 *
 * Default style: Calibri 12pt, line-height 1.5, A4 page layout.
 *
 * Tampilan: A4 Portrait — halaman putih dengan shadow di atas
 * background abu-abu, toolbar sticky, tinggi minimal 700px.
 *
 * Custom Plugin:
 * - PageLayout — tombol toolbar untuk mengatur margin & ukuran kertas
 *   via modal Bootstrap, hasilnya seperti Page Setup di Microsoft Word.
 */

import { ClassicEditor, Essentials, Plugin, ButtonView } from 'ckeditor5';
import { Bold, Italic, Underline, Strikethrough } from 'ckeditor5';
import { Heading } from 'ckeditor5';
import { Font } from 'ckeditor5';
import { Highlight } from 'ckeditor5';
import { Alignment } from 'ckeditor5';
import { List, TodoList } from 'ckeditor5';
import { PasteFromOffice } from 'ckeditor5';
import { Link } from 'ckeditor5';
import { Table, TableToolbar, TableCellProperties, TableProperties } from 'ckeditor5';
import { HorizontalLine } from 'ckeditor5';
import { BlockQuote } from 'ckeditor5';
import { RemoveFormat } from 'ckeditor5';
import { Indent, IndentBlock } from 'ckeditor5';

// Import CKEditor 5 theme CSS
import 'ckeditor5/ckeditor5.css';

// ========================================================================
// PLUGIN: PageLayout — tombol "Page Layout" di toolbar CKEditor
// ========================================================================
// Saat diklik, memicu modal Bootstrap #pageLayoutModal untuk mengatur
// margin (atas, bawah, kiri, kanan) dan ukuran kertas (A4, Letter, Legal).
// ========================================================================
class PageLayout extends Plugin {
    init() {
        const editor = this.editor;
        editor.ui.componentFactory.add('pageLayout', (locale) => {
            const button = new ButtonView(locale);
            button.set({
                label: 'Page Layout',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path d="M4 3h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm1 2v14h14V5H5zm2 2h10v2H7V7zm0 4h10v2H7v-2zm0 4h7v2H7v-2z" fill="currentColor"/></svg>',
                tooltip: 'Page Layout (Margin & Ukuran Kertas)',
                withText: false,
            });
            button.on('execute', () => {
                const modalEl = document.getElementById('pageLayoutModal');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            });
            return button;
        });
    }
}

// ========================================================================
// PLUGIN: LineHeight — Line spacing seperti di Word
// ========================================================================
// Satu tombol yang memunculkan panel sederhana dengan opsi line-height.
// ========================================================================
class CustomLineHeight extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('customLineHeight', (locale) => {
            const btn = new ButtonView(locale);
            btn.set({
                label: 'Line Spacing',
                tooltip: 'Line Spacing (1.0, 1.15, 1.5, 2.0, 2.5, 3.0)',
                withText: true,
            });
            btn.on('execute', () => {
                // Cycle through line heights
                const vals = ['1.0', '1.15', '1.5', '2.0', '2.5', '3.0'];
                editor.model.change(writer => {
                    const blocks = Array.from(
                        editor.model.document.selection.getSelectedBlocks()
                    );
                    for (const block of blocks) {
                        const current = block.getAttribute('customLineHeight') || '1.5';
                        const idx = vals.indexOf(current);
                        const next = vals[(idx + 1) % vals.length];
                        writer.setAttribute('customLineHeight', next, block);
                        try {
                            const viewElem = editor.editing.mapper.toViewElement(block);
                            if (viewElem) {
                                const domElem = editor.editing.view.domConverter.mapViewToDom(viewElem);
                                if (domElem) domElem.style.lineHeight = next;
                            }
                        } catch(e) {}
                    }
                });
                editor.editing.view.focus();
            });
            return btn;
        });
    }
}

// ========================================================================
// INISIALISASI
// ========================================================================

document.addEventListener('DOMContentLoaded', () => {
    const editorElement = document.querySelector('#templateContent');
    if (!editorElement) return;

    ClassicEditor.create(editorElement, {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Bold, Italic, Underline, Strikethrough,
            Heading,
            Font,
            Highlight,
            Alignment,
            List, TodoList,
            PasteFromOffice,
            Link,
            Table, TableToolbar, TableCellProperties, TableProperties,
            HorizontalLine,
            BlockQuote,
            RemoveFormat,
            Indent, IndentBlock,
            PageLayout,
            CustomLineHeight,
        ],
        toolbar: {
            items: [
                'undo', 'redo',
                '|', 'heading',
                '|', 'fontFamily', 'fontSize', 'fontColor', 'highlight',
                '|', 'bold', 'italic', 'underline', 'strikethrough',
                '|', 'alignment',
                '|', 'customLineHeight', 'bulletedList', 'numberedList', 'todoList',
                '|', 'outdent', 'indent',
                '|', 'blockQuote', 'horizontalLine',
                '|', 'insertTable', 'link',
                '|', 'pageLayout',
                '|', 'removeFormat',
            ],
            shouldNotGroupWhenFull: true,
        },
        placeholder: 'Tulis konten template surat di sini...',
        fontFamily: {
            options: [
                'default',
                'Calibri, Arial, sans-serif',
                'Arial, Helvetica, sans-serif',
                'Times New Roman, serif',
                'Courier New, monospace',
                'Tahoma, Geneva, sans-serif',
                'Verdana, Geneva, sans-serif',
            ]
        },
        fontSize: {
            options: [
                8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72
            ]
        },
        table: {
            contentToolbar: [
                'tableColumn', 'tableRow', 'mergeTableCells',
                'tableProperties', 'tableCellProperties'
            ]
        },
        // Tab = 2 spasi
        typing: {
            tabSpaces: 2
        },
    }).then(editor => {
        // Simpan instance editor untuk akses global
        window.ckEditorInstance = editor;

        // Apply initial page layout padding dari hidden fields
        setTimeout(() => {
            const editable = editor.editing.view.getDomRoot();
            if (editable) {
                const top = document.getElementById('inputMarginTop')?.value || 25;
                const bottom = document.getElementById('inputMarginBottom')?.value || 25;
                const left = document.getElementById('inputMarginLeft')?.value || 25;
                const right = document.getElementById('inputMarginRight')?.value || 25;
                editable.style.padding = top + 'mm ' + right + 'mm ' + bottom + 'mm ' + left + 'mm';
            }
        }, 200);

        // ====================================================================
        // PLACEHOLDER BUILDER — API untuk menyisipkan placeholder
        // ====================================================================
        window.insertPlaceholder = (placeholderText) => {
            if (editor.state !== 'ready') return;

            editor.model.change(writer => {
                const position = editor.model.document.selection.getFirstPosition();
                if (!position) return;
                writer.insertText(placeholderText, position);
            });

            editor.editing.view.focus();
        };
    }).catch(error => {
        console.error('CKEditor 5 initialization error:', error);
    });
});
