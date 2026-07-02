<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Identity Verification | HPI System</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
   <style>
      body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; }
      
      /* Dynamic Theme Colors */
      .theme-submitter { --brand-color: #6c757d; --gradient: linear-gradient(135deg, #495057 0%, #6c757d 100%); }
      .theme-approver { --brand-color: #1e3c72; --gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
      .theme-buyer { --brand-color: #0d6efd; --gradient: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%); }

      .profile-card { border: none; border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; background: white; }
      .profile-header { background: var(--gradient); padding: 40px 20px; color: white; text-align: center; }
      
      .avatar-circle { width: 90px; height: 90px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: var(--brand-color); font-size: 2rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
      
      .verified-stamp { color: #27ce80; font-weight: 800; text-transform: uppercase; border: 3px solid #27ce80; display: inline-block; padding: 5px 15px; border-radius: 8px;; background: white; width: 100%}
      
      .label-custom { color: #8e94a9; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 2px; }
      .value-custom { color: #2d3436; font-weight: 600; margin-bottom: 15px; }
      
      .buyer-contact-box { background: #f8f9fa; border-radius: 12px; padding: 12px; border: 1px solid #e9ecef; }
   </style>
</head>
<body class="theme-{{ $person['type'] }}"> <div class="container py-5">
   <div class="row justify-content-center">
      <div class="col-md-5">
         <div class="card profile-card">
               <div class="profile-header">
                  <div class="avatar-circle">
                     @if($person['type'] === 'submitter') <i class="text-dark ri-edit-box-line"></i>
                     @elseif($person['type'] === 'approver') <i class="text-dark ri-shield-check-line"></i>
                     @else <i class="text-dark ri-user-shared-line"></i> @endif
                  </div>
                  <h4 class="mb-0 fw-bold">{{ $person['name'] }}</h4>
                  <p class="opacity-75 small mb-0">{{ $person['role'] }}</p>
               </div>
               
               <div class="card-body p-4 text-center">
                  <div class="verified-stamp">
                     <i class="ri-verified-badge-fill"></i> Verified {{ ucfirst($person['type']) }}
                  </div>

                  <div class="mt-4 text-start">
                     <div class="label-custom">Organization / Entity</div>
                     <div class="value-custom">{{ $person['org'] }}</div>

                     <div class="label-custom">Document Reference</div>
                     <div class="value-custom">#{{ $transaction['transaction_number'] }}</div>

                     <div class="label-custom">Action Timestamp</div>
                     <div class="value-custom">{{ $person['date'] }}</div>

                     @if($person['type'] === 'buyer')
                           <div class="buyer-contact-box">
                              <div class="label-custom">Contact Information</div>
                              <div class="small fw-bold"><i class="ri-mail-line"></i> {{ $person['email'] }}</div>
                              <div class="small fw-bold"><i class="ri-phone-line"></i> {{ $person['phone'] }}</div>
                           </div>
                     @endif
                  </div>

                  <hr class="my-4">

                  <div class="p-3 bg-light rounded-3 text-muted" style="font-size: 0.8rem;">
                     <i class="ri-information-line"></i> This electronic signature was recorded via the HPI Asset Management portal. Tampering with this URL or the associated QR code will invalidate this verification.
                  </div>
               </div>
         </div>

         <div class="text-center mt-4 text-muted small">
               <p>&copy; 2026 PT. Hisamitsu Pharma Indonesia<br>Document Integrity Verified via SHA-256</p>
         </div>
      </div>
   </div>
</div>

</body>
</html>