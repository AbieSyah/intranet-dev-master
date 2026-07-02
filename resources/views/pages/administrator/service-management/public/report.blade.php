<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Approval Requisition</title>
   <style>
      /* * {
         border: 1px solid red;
      } */
      body {
         font-family: Arial, sans-serif;
         font-size: 7pt;
         margin: 0;
         padding: 20px;
      }
      .header {
         margin-bottom: 30px;
      }
      .header h3 {
         margin: 0;
         font-weight: bold;
      }
      .header p {
         margin: 0;
         font-size: 10pt;
      }
      .title {
         text-align: center;
         font-weight: bold;
         margin-bottom: 20px;
         font-size: 16px;
         text-decoration: underline;
      }
      .section-header {
         background-color: #a6a6a6;
         color: black;
         font-weight: bold;
         padding: 4px 8px;
         margin-top: 15px;
         margin-bottom: 10px;
      }
      table {
         width: 100%;
         border-collapse: collapse;
      }
      td {
         vertical-align: top;
         padding: 4px 0;
      }
      .label {
         width: 120px;
      }
      .colon {
         width: 15px;
         text-align: center;
      }
      .box {
         border: 1px solid black;
         min-height: 60px;
         padding: 8px;
         margin-top: 2px;
         width: 90%;
      }
      .signature-cell {
         text-align: center;
         width: 120px;
      }
      .qr-code {
         width: 80px;
         height: 80px;
         margin-bottom: 5px;
      }
      .date-signed {
         font-size: 8pt;
         text-align: center;
      }

      table {
         /* table-layout: fixed;
         width: 100%; */
         word-break: break-word;
      }

      .footer {
         width: 100%;
         position: fixed;
         bottom: 0;
      }

      /* .header {
         top: 0;
      } */
   </style>
</head>
<body>
   <div class="header">
      <h3>Hisamitsu Pharma Indonesia</h3>
      <p>Information Technology</p>
   </div>

   <div class="footer">
      Printed from INTRANET - IT Service Management System by {{ Auth::user()->employee->fullname }} - {{ Auth::user()->employee->nik }} {{ now()->format('d/m/Y H:i:s') }}
   </div>

   <div class="title">APPROVAL REQUISITION</div>

   <div class="section-header">User Request</div>
   <table>
      <tr>
         <td>
            <table>
               <tr>
                  <td class="label">Ticket Number</td>
                  <td class="colon">:</td>
                  <td>{{ $ticket->no_ticket }}</td>
               </tr>
               <tr>
                  <td class="label">release</td>
                  <td class="colon">:</td>
                  <td>{{ $ticket->created_at->format("d/m/Y") }}</td>
               </tr>
               <tr>
                  <td class="label">Request by</td>
                  <td class="colon">:</td>
                  <td>{{ $ticket->submitter->nik }} - {{ $ticket->submitter->fullname }}({{ $ticket->submitter->position->nama }} - {{ $ticket->submitter->area->name }} - {{ $ticket->submitter->department->name }})</td>
               </tr>
               @if ($ticket->type == App\Models\ServiceTicket::TYPE_IT_INITIATIVE)
                  <tr>
                     <td class="label">Employee</td>
                     <td class="colon">:</td>
                     <td>{{ $ticket->reportFor->nik }} - {{ $ticket->reportFor->fullname }}({{ $ticket->reportFor->position->nama }} - {{ $ticket->reportFor->area->name }} - {{ $ticket->reportFor->department->name }})</td>
                  </tr>
               @endif
               <tr>
                  <td class="label">Subject</td>
                  <td class="colon">:</td>
                  <td>{{ $ticket->subject }}</td>
                  <td></td>
               </tr>
               <tr>
                  <td class="label">Message</td>
                  <td class="colon">:</td>
                  <td>
                     <div class="box">
                        {{-- @foreach ($messages as $message) --}}
                           @if ($message->role == App\Models\ServiceTicketMessage::ROLE_USER || $message->role == App\Models\ServiceTicketMessage::ROLE_IT)
                              <p>[{{ $message->role == App\Models\ServiceTicketMessage::ROLE_USER? "EMPLOYEE" : "IT Response" }} - {{ strtoupper($message->sender->fullname) }} "{{ $message->created_at->format('d/m/Y H:i') }}"] : {!! strip_tags($message->message) !!}</p>
                           @endif
                        {{-- @endforeach --}}

                        @if ($ticket->messages->where('role', '!=', App\Models\ServiceTicketMessage::ROLE_SYSTEM)->count() > 1)
                           <span>... and {{ $ticket->messages->where('role', '!=', App\Models\ServiceTicketMessage::ROLE_SYSTEM)->count() - 1 }} more messages.</span>
                        @endif
                        <a target="blank" href="{{ URL::signedRoute('service-ticket.approve-workspace', ['id' => encrypt($ticket->id), 'role' => encrypt('cc')]) }}">View Details</a>
                        {{-- Manual Link: {{ URL::signedRoute('service-ticket.approve-workspace', ['id' => encrypt($ticket->id), 'role' => encrypt('cc')]) }} --}}
                     </div>
                  </td>
               </tr>
            </table>
         </td>
         <td class="signature-cell">
            <p style="margin:0 0 5px 0;">Signed</p>
            <img src="data:image/svg+xml;base64,{!! $submitter->qrcode !!}" class="qr-code" alt="QR">
            <p class="date-signed">Date: {{ $ticket->created_at->format('d/m/Y') }}</p>
         </td>
      </tr>
   </table>

   <div class="section-header">IT Handling</div>
   <div style="display: flex;">
      <table>
         <tr>
            <td>
               <table>
                  <tr>
                     <td class="label">IT Handling by</td>
                     <td class="colon">:</td>
                     @if ($itHandler)
                        <td>{{ $itHandler->nik?? 'N/A' }} - {{ $itHandler->fullname }} ({{ $ticket->it_handler_position?? "N/A" }} - {{ $ticket->it_handler_area?? "N/A" }} - {{ $ticket->it_handler_department?? "N/A" }})</td>
                     @else
                        <td>N/A</td>
                     @endif
                  </tr>
                  <tr>
                     <td class="label">Notes</td>
                     <td class="colon">:</td>
                     <td>
                        <div class="box">{{ $ticket->it_note ?? 'N/A' }}</div>
                     </td>
                  </tr>
               </table>
            </td>
            <td class="signature-cell">
               <p style="margin:0 0 5px 0;">Signed</p>
               <img src="data:image/svg+xml;base64,{!! $itHandler->qrcode?? '' !!}" class="qr-code" alt="QR">
               <p class="date-signed">Date: {{ $ticket->submitted_for_approval_at?->format('d/m/Y') ?? 'Unassigned' }}</p>
            </td>
         </tr>
      </table>
   </div>

   <div class="section-header">Approval</div>
   <table>
      <tr>
         <td>
            <table>
               <tr>
                  <td class="label">Direct Supervisor</td>
                  <td class="colon">:</td>
                  <td>{{ $supervisor->nik?? 'N/A' }} - {{ $supervisor->fullname }} ({{ $ticket->supervisor_position?? "N/A" }} - {{ $ticket->supervisor_area?? "N/A" }} - {{ $ticket->supervisor_department?? "N/A" }})</td>
               </tr>
               <tr>
                  <td class="label">Comment</td>
                  <td class="colon">:</td>
                  <td>
                     <div class="box">{{ $ticket->supervisor_note?? 'N/A' }}</div>
                  </td>
               </tr>
            </table>
         </td>
         <td class="signature-cell">
            <p style="margin:0 0 5px 0;">Signed</p>
            @if($ticket->supervisor_approval == App\Models\ServiceTicket::APPROVAL_STATUS_APPROVED)
               <img src="data:image/svg+xml;base64,{!! $supervisor->qrcode !!}" class="qr-code" alt="QR">
            @else
               <div class="box" style="display: flex; align-items: center; justify-content: center; height: 60px; width: 60px; margin: 0 auto;">
                  <span style="font-size: 8px; color: gray;">Not Approved</span>
               </div>
            @endif
            <p class="date-signed">Date: {{ $ticket->supervisor_approval_at?->format('d/m/Y')?? 'Unassigned' }}</p>
         </td>
      </tr>

      <tr>
         <td>
            <table>
               <tr>
                  <td class="label" style="padding-top: 20px;">Dept. Head</td>
                  <td class="colon" style="padding-top: 20px;">:</td>
                  <td style="padding-top: 20px;">{{ $deptHead->nik?? 'N/A' }} - {{ $deptHead->fullname }} ({{ $ticket->dept_head_position?? "N/A" }} - {{ $ticket->dept_head_area?? "N/A" }} - {{ $ticket->dept_head_department?? "N/A" }})</td>
               </tr>
               <tr>
                  <td class="label">Comment</td>
                  <td class="colon">:</td>
                  <td>
                     <div class="box" >{{ $ticket->dept_head_note?? 'N/A' }}</div>
                  </td>
               </tr>
            </table>
         </td>
         <td class="signature-cell" style="padding-top: 20px;">
            <p style="margin:0 0 5px 0;">Signed</p>
            @if($ticket->dept_head_approval == App\Models\ServiceTicket::APPROVAL_STATUS_APPROVED)
               <img src="data:image/svg+xml;base64,{!! $deptHead->qrcode !!}" class="qr-code" alt="QR">
            @else
               <div class="box" style="display: flex; align-items: center; justify-content: center; height: 60px; width: 60px; margin: 0 auto;">
                  <span style="font-size: 8px; color: gray;">Not Approved</span>
               </div>
            @endif
            <p class="date-signed">Date: {{ $ticket->dept_head_approval_at?->format('d/m/Y')?? 'Unassigned' }}</p>
         </td>
      </tr>
   </table>
</body>
</html>