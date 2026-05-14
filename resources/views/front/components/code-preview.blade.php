@props(['title' => 'POST /v1/sms/send', 'locale' => 'fr'])

<div class="dd-code-panel dd-code-panel--preview">
  <div class="dd-code-panel__bar">
    <span></span><span></span><span></span>
    <strong>{{ $title }}</strong>
  </div>
  <pre><code data-code-preview>curl -X POST \
  https://api.dream-digital.info/v1/sms/send \
  -H "Authorization: Bearer dd_live..." \
  -H "Content-Type: application/json" \
  -d '{
    "to": "+243990000000",
    "from": "DreamDigital",
    "text": "Votre code OTP est 428931"
  }'

HTTP/1.1 200 OK
{
  "id": "sms_a2b3c4d5",
  "status": "delivered",
  "cost": 0.0089
}</code></pre>
</div>
