<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Asset Verification | HPI System</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
   <style>
      body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; }
      
      /* Dynamic Theme Colors berdasarkan Status Aset (Active, Maintenance, Disposed) */
      .theme-active { --brand-color: #1e3c72; --gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
      .theme-maintenance { --brand-color: #fd7e14; --gradient: linear-gradient(135deg, #fd7e14 0%, #f76707 100%); }
      
      .profile-card { border: none; border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; background: white; }
      .profile-header { background: var(--gradient); padding: 40px 20px; color: white; text-align: center; }
      
      .avatar-circle { width: 90px; height: 90px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: var(--brand-color); font-size: 2rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
      
      .verified-stamp { color: #27ce80; font-weight: 800; text-transform: uppercase; border: 3px solid #27ce80; display: inline-block; padding: 5px 15px; border-radius: 8px; background: white; width: 100%}
      
      .label-custom { color: #8e94a9; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 2px; }
      .value-custom { color: #2d3436; font-weight: 600; margin-bottom: 15px; }
      
      .spec-box { background: #f8f9fa; border-radius: 12px; padding: 15px; border: 1px solid #e9ecef; margin-bottom: 15px}
   </style>
</head>
<body class="theme-{{ $asset['status'] ?? 'active' }}"> 
<div class="container py-5">
   <div class="row justify-content-center">
      <div class="col-11 col-md-9 col-lg-5">
         <div class="card profile-card">
               <div class="profile-header">
                  <div class="avatar-circle">
                     <i class="text-dark ri-computer-line"></i>
                  </div>
                  <h4 class="mb-0 fw-bold">{{ $asset['asset_code'] }}</h4>
                  <p class="opacity-75 small mb-0">{{ $asset['brand'] }}</p>
               </div>
               
               <div class="card-body p-4 text-center">
                  <div class="verified-stamp">
                     <i class="ri-checkbox-circle-fill"></i> Genuine Registered Asset
                  </div>

                  <div class="mt-4 text-start">
                     {{-- Tipe, Brand / Merk, Owner, Registered On, Hardware Spec, Software Spec --}}
                     <div class="label-custom">Tipe</div>
                     <div class="value-custom">{{ $asset['asset_type'] }}</div>

                     <div class="label-custom">Brand / Merk</div>
                     <div class="value-custom"><i class="ri-price-tag-3-line text-muted me-1"></i> {{ $asset['brand'] }}</div>
                     
                     <div class="label-custom">Owner</div>
                     <div class="value-custom"><i class="ri-user-line text-muted me-1"></i> {{ $asset['employee'] }}</div>

                     <div class="label-custom">Area Penempatan</div>
                     <div class="value-custom"><i class="ri-map-pin-line text-muted me-1"></i> {{ $asset['area'] }}</div>

                     <div class="label-custom">Tanggal Pendaftaran</div>
                     <div class="value-custom"><i class="ri-calendar-line text-muted me-1"></i> {{ $asset['year_registered'] }}</div>

                     <div class="label-custom">Hardware Specification</div>
                     <div class="spec-box small text-secondary">
                        {{ $asset['hardware'] }}
                     </div>

                     <div class="label-custom">Software Specification</div>
                     <div class="spec-box small text-secondary">
                        {{ $asset['software'] }}
                     </div>
                  </div>

                  <hr class="my-4">

                  <div class="p-3 bg-light rounded-3 text-muted" style="font-size: 0.8rem;">
                     <i class="ri-information-line"></i> Data aset ini terdaftar secara resmi di dalam sistem portal IT Asset Management HPI. Segala bentuk modifikasi fisik tanpa dokumen pengajuan internal dianggap tidak valid.
                  </div>
               </div>
         </div>

         <div class="text-center mt-4 text-muted small">
               <p>&copy; 2026 PT. Hisamitsu Pharma Indonesia<br>Asset Integrity Verified via SHA-256</p>
         </div>
      </div>
   </div>
</div>
</body>
</html>