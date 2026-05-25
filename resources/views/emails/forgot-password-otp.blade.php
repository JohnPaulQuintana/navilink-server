<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
</head>

<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,sans-serif;">

<div style="
    background:#f4f7fb;
    padding:40px 20px;
    font-family:Arial, sans-serif;
">

  <div style="
    max-width:520px;
    margin:auto;
    background:#ffffff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 12px 35px rgba(0,0,0,0.08);
  ">

    <!-- HEADER -->
    <div style="
      background:linear-gradient(135deg,#ef4444,#f97316);
      padding:32px 24px;
      text-align:center;
    ">
      <h1 style="color:#fff;margin:0;font-size:26px;">
        NaviLink
      </h1>
      <p style="color:rgba(255,255,255,0.85);font-size:13px;">
        Secure password recovery
      </p>
    </div>

    <!-- BODY -->
    <div style="padding:36px 28px;">

      <h2 style="margin:0 0 10px;color:#0f172a;">
        Reset your password
      </h2>

      <p style="color:#475569;font-size:14px;line-height:1.6;">
        We received a request to reset your password.
        Use the code below:
      </p>

      <!-- OTP -->
      <div style="text-align:center;margin:30px 0;">
        <table align="center">
          <tr>
            @foreach(str_split($otp) as $digit)
              <td style="
                width:48px;
                height:56px;
                border:2px solid #e2e8f0;
                border-radius:12px;
                background:#f8fafc;
                text-align:center;
                font-size:22px;
                font-weight:700;
                color:#ef4444;
              ">
                {{ $digit }}
              </td>
              <td style="width:10px;"></td>
            @endforeach
          </tr>
        </table>
      </div>

      <!-- INFO -->
      <div style="
        background:#fff7ed;
        border-radius:14px;
        padding:16px;
        border:1px solid #fdba74;
      ">
        <p style="margin:0;color:#9a3412;font-size:13px;">
          This code expires in <b>5 minutes</b><br>
          Never share this code with anyone
        </p>
      </div>

      <p style="margin-top:24px;font-size:12px;color:#94a3b8;text-align:center;">
        Need help? Contact NaviLink support.
      </p>

    </div>

    <!-- FOOTER -->
    <div style="
      text-align:center;
      padding:18px;
      font-size:12px;
      color:#94a3b8;
      border-top:1px solid #eef2f7;
    ">
      © {{ date('Y') }} NaviLink
    </div>

  </div>
</div>

</body>
</html>