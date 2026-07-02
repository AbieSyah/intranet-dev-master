
@extends(((Auth::user()->can('emp.menu')) ? 'layouts.general' : 'layouts.master'))

@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">     
                            <center><h5 class="fw-bold"> KEBIJAKAN PRIVACY</h5></center>
                            <center><i><h5 class="fw-bold"> PRIVACY POLICY</h5></i></center>        
                            <p class="fw-normal lh-lg mt-4">
                                Dalam menyediakan layanan sistem elektronik, PT Hisamitsu Pharma Indonesia senantiasa berkomitmen untuk melindungi dan menjaga informasi dan data  pribadi karyawan yang dapat diidentifikasi secara mandiri sejalan dengan ketentuan yang diatur oleh Undang – Undang Nomor 27 Tahun 2022 tentang Perlindungan Data Pribadi.
                                <br>
                                <i>
                                    In providing electronic system services, PT Hisamitsu Pharma Indonesia is always committed to protecting and maintaining employee information and personal data that can be independently identified in line with the provisions stipulated by Law Number 27 of 2022 concerning Personal Data Protection.
                                </i>
                            </p>
                            <ul class="list-unstyled">
                                <li class="fw-semibold">Ketentuan Penggunaan Situs INTRANET</li>
                                <li class="fw-semibold"><i class="fst-italic">Term of use of the INTRANET Site</i></li>
                                <li>
                                    <ul class="list-unstyled mt-2">
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    1.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Ketentuan penggunaan Situs INTRANET ini merupakan kebijakan dari PT Hisamitsu Pharma Indonesia terkait pengelolaan teknis Situs serta penggunaan data pribadi yang di kumpulkan dan di simpan melalui Situs ini.
                                                        <br>
                                                        <i class="fst-italic">
                                                            The terms of use of the INTRANET Site are the policies of PT Hisamitsu Pharma Indonesia regarding the technical management of the Site and the use of personal data collected and stored through this Site.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    2.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Ketentuan penggunaan Situs INTRANET ini merupakan bagian dari syarat penggunaan Situs ini. Dengan menggunakan Situs ini Anda telah membaca, mengetahui dan menyetujui Ketentuan Penggunaan Situs INTRANET.
                                                        <br>
                                                        <i class="fst-italic">
                                                            The terms of use of the INTRANET Site are part of the terms of use of this Site. By using this Site you have read, understood and agreed to the Terms of Use of the INTRANET Site.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    3.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Ketentuan penggunaan Situs INTRANET ini dapat berubah sewaktu – waktu sesuai dengan perkembangan kebijakan yang ada dan di tetapkan, Anda disarankan untuk dapat selalu memahami perubahan kebijakan yang berlaku.
                                                        <br>
                                                        <i class="fst-italic">
                                                            The terms of use of the INTRANET Site may change at any time in accordance with the development of existing and stipulated policies, you are advised to always understand the changes to the applicable policies.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    4.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Ketentuan pengelolaan data pribadi pada Situs INTRANET merupakan bagian yang tidak terpisahkan dari ketentuan pelindungan data pribadi yang di tetapkan oleh Perusahaan yang di tuangkan dalam kebijakan pelindungan data pribadi dalam upaya pemenuhan ketentuan hukum pada peraturan perundangan terkait.
                                                        <br>
                                                        <i class="fst-italic">
                                                            The provisions for managing personal data on the INTRANET Site are an inseparable part of the provisions for protecting personal data set by the Company which are stated in the personal data protection policy in an effort to fulfill the legal provisions of the relevant laws and regulations.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="list-unstyled">
                                <li class="fw-semibold">Pengelolaan Situs INTRANET</li>
                                <li class="fw-semibold"><i class="fst-italic">INTRANET Site Management</i></li>
                                <li>
                                    <ul class="list-unstyled mt-2">
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    1.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Situs INTRANET adalah Sistem Informasi yang berkaitan dengan kepersonaliaan dari Internal Karyawan.
                                                        <br>
                                                        <i class="fst-italic">
                                                            INTRANET Site is an Information System related to Internal Employee personnel.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    2.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Perubahan pada Situs INTRANET ini dapat kami lakukan dari waktu ke waktu tanpa pemberitahuan sebelumnya.
                                                        <br>
                                                        <i class="fst-italic">
                                                            Changes to this INTRANET Site can be made from time to time without prior notice.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    3.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Beberapa konten dari Situs INTRANET di persiapkan oleh Departemen HRD & GA dimana konten tersebut berdiri sendiri dan dapat di akses melalui Hypertext Link.
                                                        <br>
                                                        <i class="fst-italic">
                                                            Some content of the INTRANET Site is prepared by the HRD & GA Department where the content stands alone and can be accessed via Hypertext Link.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    4.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Hak Cipta dari setiap konten, gambar atau materi audio-visual pada Situs INTRANET sepenuhnya di lindungi dan merupakan milik PT Hisamitsu Pharma Indonesia, penggunaan merek dagang, ikon dan logo tanpa ijin terlebih dahulu dari pejabat yang berwenang adalah dilarang.
                                                        <br>
                                                        <i class="fst-italic">
                                                            The copyright of any content, images or audio-visual material on the INTRANET Site is fully protected and is the property of PT Hisamitsu Pharma Indonesia, use of trademarks, icons and logos without prior permission from the authorized official is prohibited.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    5.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Situs INTRANET dilindungi beberapa metode kemanan:
                                                        <br>
                                                        <i class="fst-italic">
                                                            INTRANET sites are protected by several security methods:
                                                        </i>
                                                    </p>
                                                    <ul>
                                                        <li>
                                                            <p class="fw-normal lh-lg">
                                                                Situs INTRANET dilindungi menggunakan Kemanan SSL Versi 3 SHA-256 With RSA Encryption yang selalu di perbaharui setiap tahunnya.
                                                                <br>
                                                                <i class="fst-italic">
                                                                    The INTRANET site is protected using SSL Security Version 3 SHA-256 With RSA Encryption which is always updated annually.
                                                                </i> 
                                                            </p> 
                                                        </li>
                                                        <li>
                                                            <p class="fw-normal lh-lg">
                                                                Setiap akses pengguna dipastikan menggunakan kata sandi dengan minimum 8 karakter dengan tanda baca dan huruf yang berbeda.
                                                                <br>
                                                                <i class="fst-italic">
                                                                    Each user access is ensured to use a password with a minimum of 8 characters with different punctuation and letters.
                                                                </i> 
                                                            </p> 
                                                        </li>
                                                        <li>
                                                            <p class="fw-normal lh-lg">
                                                                Setiap akses pengguna akan dihubungkan secara otomatis melalui masing-masing email Perusahaan atau Pribadi.
                                                                <br>
                                                                <i class="fst-italic">
                                                                    Each user access will be automatically connected via each Company or Personal email.
                                                                </i> 
                                                            </p> 
                                                        </li>
                                                        <li>
                                                            <p class="fw-normal lh-lg">
                                                                Situs INTRANET hanya dapat di akses oleh Karyawan yang terdaftar pada Departemen HRD & GA dan telah memiliki hak akses.
                                                                <br>
                                                                <i class="fst-italic">
                                                                    The INTRANET site can only be accessed by Employees registered with the HRD & GA Department and have access rights.
                                                                </i> 
                                                            </p> 
                                                        </li>
                                                        <li>
                                                            <p class="fw-normal lh-lg">
                                                                Seluruh data akan tersimpan pada server secara terpusat dan dikendalikan oleh Bagian IT.
                                                                <br>
                                                                <i class="fst-italic">
                                                                    All data will be stored on a centralized server and controlled by the IT Department
                                                                </i> 
                                                            </p> 
                                                        </li>
                                                        <li>
                                                            <p class="fw-normal lh-lg">
                                                                Situs INTRANET dilakukan pencadangan setiap hari yang dapat dipulihkan hanya oleh Bagian IT.
                                                                <br>
                                                                <i class="fst-italic">
                                                                    The INTRANET site is backed up every day which can only be restored by the IT Department.
                                                                </i> 
                                                            </p> 
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="list-unstyled">
                                <li class="fw-semibold">Pengelolaan Data Pribadi</li>
                                <li class="fw-semibold"><i class="fst-italic">Personal Data Management</i></li>
                                <li>
                                    <ul class="list-unstyled mt-2">
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    1.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Pengumpulan Data Pribadi Anda Kami lakukan dengan tidak bertentangan pada peraturan perundang-undangan yang berlaku.
                                                        <br>
                                                        <i class="fst-italic">
                                                            We collect your personal data without violating applicable laws and regulations.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    2.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Tujuan pengelolaan data pribadi di laksanakan dalam rangka penyediaan layanan Informasi bagi Karyawan dan untuk mendukung pemenuhan kewajiban hukum Perusahaan pada pihak – pihak yang berwenang.
                                                        <br>
                                                        <i class="fst-italic">
                                                            The purpose of managing personal data is carried out in order to provide information services for employees and to support the fulfillment of the Company's legal obligations to authorized parties.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    3.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Pengungkapan data pribadi Anda kepada pihak ketiga selain dari pada pihak – pihak berwenang yang oleh peraturan perundangan di izinkan menerima pengungkapan berdasarkan kepentingan hukum yang sah, wajib mendapatkan persetujuan Anda.
                                                        <br>
                                                        <i class="fst-italic">
                                                            Disclosure of your personal data to third parties other than authorized parties who are permitted by law to receive disclosure based on legitimate legal interests, must obtain your consent.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    4.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Kualitas pelindungan data pribadi menjadi perhatian kami dalam melaksanakan pengelolaannya, akses dan pengungkapan data pribadi hanya dapat di laksanakan pada saluran – saluran terbatas yang di izinkan oleh peraturan perundangan.
                                                        <br>
                                                        <i class="fst-italic">
                                                            The quality of personal data protection is our concern in carrying out its management, access and disclosure of personal data can only be carried out on limited channels permitted by law.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    5.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Kami berkomitmen untuk mengelola data pribadi Anda sesuai dengan ketentuan pelindungan data pribadi di bawah pengawasan pejabat yang berkompeten.
                                                        <br>
                                                        <i class="fst-italic">
                                                            We are committed to managing your personal data in accordance with the provisions on personal data protection under the supervision of competent officials.
                                                        </i>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    6.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Anda dapat melakukan koreksi pada data pribadi Anda berupa pengkinian keakuratan dan kelengkapan dari informasi pribadi dengan terlebih dahulu mengajukan permintaan baik secara lisan maupun tertulis kepada Kepala Bagian HRD melalui:
                                                        <br>
                                                        <i class="fst-italic">
                                                            You can make corrections to your personal data in the form of updating the accuracy and completeness of personal information by first submitting a request either verbally or in writing to the Head of HRD Section via:
                                                        </i>
                                                        <br>
                                                        Email : phontas@hisamitsu.co.id
                                                        <br>
                                                        Handphone : 0811-313-7079
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="list-unstyled">
                                <li class="fw-semibold">Kerahasiaan Data</li>
                                <li class="fw-semibold"><i class="fst-italic">Data Confidentiality</i></li>
                                <li>
                                    <ul class="list-unstyled mt-2">
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    1.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Situs ini hanya dapat di akses oleh orang yang terdaftar sebagai karyawan PT Hisamitsu Pharma Indonesia.
                                                        <br>
                                                        <i class="fst-italic">
                                                            This site can only be accessed by people who are registered as employees of PT Hisamitsu Pharma Indonesia.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    2.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Setiap karyawan dilarang keras mengambil data dan informasi yang terkandung di dalamnya untuk kepentingan di luar perusahaan.
                                                        <br>
                                                        <i class="fst-italic">
                                                            Every employee is strictly prohibited from taking data and information contained therein for interests outside the company.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 fw-normal">
                                                    3.
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="fw-normal lh-lg">
                                                        Dilarang melakukan pemotretan atau screenshoot dari informasi yang terkandung di dalamnya untuk kepentingan di luar perusahaan.
                                                        <br>
                                                        <i class="fst-italic">
                                                            It is prohibited to take pictures or screenshots of the information contained therein for interests outside the company.
                                                        </i> 
                                                    </p> 
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="list-unstyled">
                                <li class="fw-semibold">
                                    <p class="lh-lg">
                                        Apabila anda membutuhkan bantuan dari layanan penggunaan Situs INTRANET ini, silahkan laporkan keluhan anda melalui <a
                                            href="https://helpdesk.hisamitsu.co.id" target="_blank">https://helpdesk.hisamitsu.co.id</a>
                                        <br>
                                        <i class="fst-italic">
                                            If you need help from the INTRANET Site usage service, please report your complaint at <a
                                                href="https://helpdesk.hisamitsu.co.id" target="_blank">https://helpdesk.hisamitsu.co.id</a>
                                        </i>
                                    </p>
                                </li>
                            </ul>
                        </div>                          
                    </div><!-- end card -->
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
@section('script')
@endsection
@section('javascript')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endsection
