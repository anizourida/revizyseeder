<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification d'e-mail — Revizy</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2E76B6, #00AAA4); padding: 32px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Revizy Enseignants</h1>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1a1a2e; margin: 0 0 16px; font-size: 20px;">Bonjour {{ $teacher->name }},</h2>
                            <p style="color: #444; font-size: 15px; line-height: 1.6; margin: 0 0 24px;">
                                Merci de vous être inscrit(e) sur Revizy. Pour activer votre compte, veuillez vérifier votre adresse e-mail en cliquant sur le bouton ci-dessous.
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="border-radius: 8px; background: linear-gradient(135deg, #2E76B6, #00AAA4);">
                                        <a href="{{ $verificationUrl }}" target="_blank" style="display: inline-block; padding: 14px 36px; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; letter-spacing: 0.5px;">
                                            Vérifier mon adresse e-mail
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="color: #888; font-size: 13px; line-height: 1.5; margin: 28px 0 0; border-top: 1px solid #eee; padding-top: 20px;">
                                Si vous n'avez pas créé de compte, vous pouvez ignorer cet e-mail. Ce lien expire dans 24 heures.
                            </p>
                            <p style="color: #aaa; font-size: 12px; margin: 16px 0 0; word-break: break-all;">
                                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                                <a href="{{ $verificationUrl }}" style="color: #2E76B6;">{{ $verificationUrl }}</a>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 40px; text-align: center; border-top: 1px solid #eee;">
                            <p style="color: #aaa; font-size: 12px; margin: 0;">© {{ date('Y') }} Revizy. Tous droits réservés.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
