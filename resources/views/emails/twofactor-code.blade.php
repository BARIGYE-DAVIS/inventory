<!DOCTYPE html>
<html>
  <body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; color:#111827;">
    <div style="max-width:560px;margin:24px auto;padding:24px;border:1px solid #e5e7eb;border-radius:12px;">
      <h1 style="font-size:20px;margin:0 0 12px;">Verification Code</h1>
      <p style="margin:0 0 12px;">Use the following 6-digit code to verify your login. It expires in 3 minutes.</p>
      <div style="font-size:28px;font-weight:700;letter-spacing:8px;background:#f3f4f6;padding:12px 16px;text-align:center;border-radius:8px;">
        {{ $code }}
      </div>
      <p style="margin:12px 0 0;color:#6b7280;font-size:12px;">If you did not attempt to sign in, please ignore this email.</p>
    </div>
  </body>
</html>