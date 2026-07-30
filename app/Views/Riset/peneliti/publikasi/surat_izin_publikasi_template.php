<?php

/**
 * @var string $nama_peneliti
 * @var string $nim
 * @var string $institusi
 * @var string $judul_riset
 * @var string $nama_publikasi
 * @var string $jenis_jurnal
 * @var string $kategori
 * @var string $issn
 * @var string $scope
 * @var string $alamat_web
 * @var string $waktu_penelitian
 */

$nama_peneliti   = $nama_peneliti ?? '-';
$nim             = $nim ?? '-';
$institusi       = $institusi ?? '-';
$judul_riset     = $judul_riset ?? '-';
$nama_publikasi  = $nama_publikasi ?? '-';
$jenis_jurnal    = $jenis_jurnal ?? '-';
$kategori        = $kategori ?? '-';
$issn            = $issn ?? '-';
$scope           = $scope ?? '-';
$alamat_web      = $alamat_web ?? '-';
$waktu_penelitian = $waktu_penelitian ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Cetak Surat Izin Publikasi') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        @media print {
            @page {
                size: A4;
                margin: 2cm;
            }
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
            }
            .card {
                box-shadow: none !important;
                border: none !important;
            }
            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>

<div class="container py-5">
    <!-- Main Certificate/Letter Card -->
    <div class="card border-0 shadow-lg mx-auto" style="max-width: 800px; border-radius: 0; font-family: 'Times New Roman', Times, serif; background: #fff; border: 1px solid #e0e0e0;">
        <div id="printableArea" class="card-body py-4 px-5">
            <?php ob_start(); ?>

            <!-- Kop Surat -->
            <?php if (isset($pengaturan['logo_kop']) && !empty($pengaturan['logo_kop'])): ?>
                <div class="mb-4 border-bottom border-3 border-dark pb-3 text-center">
                    <img src="<?= base_url('uploads/riset/pengaturan/' . $pengaturan['logo_kop']) ?>" alt="Kop Surat" style="width: 100%; max-height: 200px; object-fit: contain;">
                </div>
            <?php else: ?>
                <div class="position-relative d-flex justify-content-center align-items-center mb-4 border-bottom border-3 border-dark pb-3">
                    <div style="position: absolute; left: 5px;">
                        <img src="<?= base_url('assets/img/logo surat izin.png') ?>" alt="Logo" style="height: 115px;">
                    </div>
                    <div class="text-center w-100">
                        <h6 class="mb-0 text-dark">PEMERINTAH KOTA YOGYAKARTA</h6>
                        <h6 class="mb-0 text-dark">DINAS KESEHATAN</h6>
                        <h5 class="fw-bold mb-1 text-dark">RUMAH SAKIT UMUM DAERAH</h5>
                        <p class="mb-0 mt-1 text-dark" style="font-size: 13px;">Jl. Wirosaban 1, Yogyakarta, Daerah Istimewa Yogyakarta 55162</p>
                        <p class="mb-0 text-dark" style="font-size: 13px;">Telepon (0274) 371195, 386692</p>
                        <p class="mb-0 text-dark" style="font-size: 13px;">Laman rumahsakitjogja.jogjakota.go.id; Pos-el rsud@jogjakota.go.id</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mb-3">
                <h5 class="fw-bold text-decoration-underline mb-0 text-dark" style="letter-spacing: 1.5px;">SURAT IZIN PUBLIKASI PENELITIAN</h5>
                <p class="mb-0 fw-bold text-dark">No. <?= !empty($nomor_surat) ? esc($nomor_surat) : '-' ?></p>
            </div>

            <!-- Isi Surat -->
            <div class="content mb-3" style="font-size: 14px; text-align: justify; color: #000; line-height: 1.35;">
                <p class="mb-2">Dengan ini Direktur RSUD Kota Yogyakarta memberikan izin publikasi hasil penelitian kepada :</p>

                <table class="table table-borderless ms-3 mb-3" style="font-size: 14px; color: #000;">
                    <tr>
                        <td width="200" class="py-1 px-1">Nama</td>
                        <td width="20" class="py-1 px-1">:</td>
                        <td class="fw-bold py-1 px-1"><?= esc($nama_peneliti) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">NIM/NIDN</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 fw-bold"><?= esc($nim) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">Institusi</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 fw-bold"><?= esc($institusi) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1" style="vertical-align: top;">Judul Penelitian</td>
                        <td class="py-1 px-1" style="vertical-align: top;">:</td>
                        <td class="fw-bold py-1 px-1" style="line-height: 1.5; text-align: justify;"><?= esc($judul_riset) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">Waktu Penelitian</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 fw-bold"><?= esc($waktu_penelitian) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="py-2 px-1">Untuk dipublikasi pada :</td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">Jenis jurnal</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 fw-bold"><?= esc($jenis_jurnal) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">Kategori</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 fw-bold"><?= esc($kategori) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">Nama Publikasi</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 fw-bold"><?= esc($nama_publikasi) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">ISSN</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 fw-bold"><?= esc($issn) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">Scope/bidang</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 fw-bold"><?= esc($scope) ?></td>
                    </tr>
                    <tr>
                        <td class="py-1 px-1">Alamat Web</td>
                        <td class="py-1 px-1">:</td>
                        <td class="py-1 px-1 text-decoration-underline fw-bold"><?= esc($alamat_web) ?></td>
                    </tr>
                </table>

                <p class="mt-2 mb-2">Demikian surat izin ini kami buat untuk dapat dipergunakan sebagaimana mestinya.</p>
            </div>

            <!-- Tanda Tangan -->
            <div class="row mt-2 text-dark">
                <div class="col-7" style="padding-top: 80px;">
                    <!-- PARAF_TABLE_PLACEHOLDER -->
                </div>
                <div class="col-5 text-start">
                    <?php
                    $bulanIndo = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ];
                    $tanggal = date('d') . ' ' . $bulanIndo[(int)date('m')] . ' ' . date('Y');
                    ?>
                    <p class="mb-0">Yogyakarta, <?= $tanggal ?></p>
                    <p class="mb-0"><?= isset($pengaturan['jabatan']) && !empty($pengaturan['jabatan']) ? esc($pengaturan['jabatan']) : 'Direktur' ?>,</p>

                    <div style="height: 80px;"></div>

                    <p class="fw-bold mb-0"><?= isset($pengaturan['nama_pejabat']) && !empty($pengaturan['nama_pejabat']) ? esc($pengaturan['nama_pejabat']) : 'dr. Ariyudi Yunita, MMR' ?></p>
                    <p class="mb-0"><?= isset($pengaturan['pangkat']) && !empty($pengaturan['pangkat']) ? esc($pengaturan['pangkat']) : 'Pembina Utama Muda (IV/c)' ?></p>
                    <p class="small mb-0">NIP. <?= isset($pengaturan['nip_pejabat']) && !empty($pengaturan['nip_pejabat']) ? esc($pengaturan['nip_pejabat']) : '196706262002122003' ?></p>
                </div>
            </div>
            <?php $letter_content = ob_get_clean(); ?>

            <?php ob_start(); ?>
            <table class="text-center mt-0" style="font-size: 9px; width: 85%; border-collapse: collapse; margin-left: 0; font-family: 'Times New Roman', Times, serif; color: black;">
                <thead>
                    <tr>
                        <th colspan="2" style="border: 1px solid black; padding: 2px;">Paraf Koordinasi</th>
                        <th colspan="2" style="border: 1px solid black; padding: 2px;">Paraf Hirarki</th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; padding: 2px; width: 35%;">Jabatan</th>
                        <th style="border: 1px solid black; padding: 2px; width: 15%;">Paraf</th>
                        <th style="border: 1px solid black; padding: 2px; width: 35%;">Jabatan</th>
                        <th style="border: 1px solid black; padding: 2px; width: 15%;">Paraf</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-start" style="border: 1px solid black; padding: 2px; font-weight: 500;">&nbsp;</td>
                        <td style="border: 1px solid black; padding: 2px; height: 18px;"></td>
                        <td class="text-start" style="border: 1px solid black; padding: 2px; font-weight: 500;">Wadir Umum dan Keu</td>
                        <td style="border: 1px solid black; padding: 2px; height: 18px;"></td>
                    </tr>
                    <tr>
                        <td class="text-start" style="border: 1px solid black; padding: 2px; font-weight: 500;">&nbsp;</td>
                        <td style="border: 1px solid black; padding: 2px; height: 18px;"></td>
                        <td class="text-start" style="border: 1px solid black; padding: 2px; font-weight: 500;">Ka Bag Umum</td>
                        <td style="border: 1px solid black; padding: 2px; height: 18px;"></td>
                    </tr>
                    <tr>
                        <td class="text-start" style="border: 1px solid black; padding: 2px; font-weight: 500;">&nbsp;</td>
                        <td style="border: 1px solid black; padding: 2px; height: 18px;"></td>
                        <td class="text-start" style="border: 1px solid black; padding: 2px; font-weight: 500;">Plt. Ka.Timja Bag. Kepeg</td>
                        <td style="border: 1px solid black; padding: 2px; height: 18px;"></td>
                    </tr>
                </tbody>
            </table>
            <?php $tabel_paraf_html = ob_get_clean(); ?>

            <!-- Halaman 1 (Untuk Peneliti) -->
            <?= str_replace('<!-- PARAF_TABLE_PLACEHOLDER -->', '', $letter_content) ?>

            <!-- Page Break -->
            <div style="page-break-before: always;" class="page-break"></div>

            <!-- Halaman 2 (Untuk Arsip / Internal) -->
            <?= str_replace('<!-- PARAF_TABLE_PLACEHOLDER -->', $tabel_paraf_html, $letter_content) ?>
        </div>
    </div>
    <div class="text-center mt-4 no-print">
        <button onclick="window.close()" class="btn btn-outline-dark px-4 py-2 rounded-pill fw-bold me-2">
            Tutup
        </button>
        <button onclick="downloadPDF()" class="btn btn-danger px-4 py-2 rounded-pill fw-bold shadow-sm">
            Download PDF
        </button>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {
        @page {
            size: A4;
            margin: 2cm;
        }

        .no-print {
            display: none !important;
        }

        body {
            background-color: white !important;
        }

        .card {
            box-shadow: none !important;
            border: none !important;
        }

        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function downloadPDF() {
        window.scrollTo(0, 0); // Fix for html2canvas shifting bug when scrolled
        var element = document.getElementById('printableArea');
        
        // Temporarily hide elements that shouldn't be printed (html2canvas ignores @media print)
        var noPrintElements = element.querySelectorAll('.d-print-none');
        noPrintElements.forEach(el => el.style.display = 'none');
        
        var opt = {
            margin:       [10, 15, 10, 15],
            filename:     'Surat_Izin_Publikasi_<?= str_replace(' ', '_', $nama_peneliti ?? '') ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, scrollY: 0 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak:    { mode: ['css', 'legacy'] }
        };
        
        html2pdf().set(opt).from(element).save().then(function() {
            // Restore hidden elements
            noPrintElements.forEach(el => el.style.display = '');
        });
    }
</script>

</body>
</html>