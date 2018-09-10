    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <title></title> <!-- the <title> tag shows on email notifications on Android 4.4. -->
    <style type="text/css">

        /* ensure that clients don't add any padding or spaces around the email design and allow us to style emails for the entire width of the preview pane */
        body,
        #bodyTable {
            height:100% !important;
            width:100% !important;
            margin:0;
            padding:0;
        }

        /* Ensures Webkit- and Windows-based clients don't automatically resize the email text. */
        body,
        table,
        td,
        p,
        a,
        li,
        blockquote {
            -ms-text-size-adjust:100%;
            -webkit-text-size-adjust:100%;
        }

        /* Forces Yahoo! to display emails at full width */
        .thread-item.expanded .thread-body .body, .msg-body {
            width: 100% !important;
            display: block !important;
        }

        /* Forces Hotmail to display emails at full width */
        .ReadMsgBody,
        .ExternalClass {
            width: 100%;
            background-color: #f4f4f4;
        }

        /* Forces Hotmail to display normal line spacing. */
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height:100%;
        }

        /* Resolves webkit padding issue. */
        table {
            border-spacing:0;
        }

        /* Resolves the Outlook 2007, 2010, and Gmail td padding issue, and removes spacing around tables that Outlook adds. */
        table,
        td {
            border-collapse:collapse;
            mso-table-lspace:0pt;
            mso-table-rspace:0pt;
        }

        /* Corrects the way Internet Explorer renders resized images in emails. */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* Ensures images don't have borders or text-decorations applied to them by default. */
        img,
        a img {
            border:0;
            outline:none;
            text-decoration:none;
        }

        /* Styles Yahoo's auto-sensing link color and border */
        .yshortcuts a {
            border-bottom: none !important;
        }

        /* Styles the tel URL scheme */
        a[href^=tel],
        .mobile_link,
        .mobile_link a {
            color:#222222 !important;
            text-decoration: underline !important;
        }

        .center{
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
        }

        /* Media queries for when the viewport is smaller than the default email width but not too narrow. */
        @media screen and (max-device-width: 600px), screen and (max-width: 600px) {

            /* Constrains email width for small screens */
            table[class="email-container"] {
                width: 100% !important;
            }
            /* Constrains tables for small screens */
            table[class="fluid"] {
                width: 100% !important;
            }

            /* Forces images to resize to full width of their container */
            img[class="fluid"],
            img[class="force-col-center"] {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
            }
            /* And centers these ones */
            img[class="force-col-center"] {
                margin: auto !important;
            }

            /* Forces table cells into rows */
            td[class="force-col"],
            td[class="force-col-center"] {
                display: block !important;
                width: 100% !important;
                clear: both;
            }
            /* And centers these ones */
            td[class="force-col-center"] {
                text-align: center !important;
            }

            /* Forces table cells into rows */
            /* Floats a previously stacked image to the left */
            img[class="col-3-img-l"] {
                float: left;
                margin: 0 15px 15px 0;
            }
            /* Floats a previously stacked image to the right */
            img[class="col-3-img-r"] {
                float: right;
                margin: 0 0 15px 15px;
            }

            /* Makes buttons full width */
            table[class="button"] {
                width: 100% !important;
            }

        }

        /* Media queries for when the viewport is narrow. */
        /* Rules prefixed with 'hh-' (for 'handheld') repeat much of what's above, but these don't trigger until the smaller screen width. */
        @media screen and (max-device-width: 425px), screen and (max-width: 425px) {

            /* Helper only visible on handhelds. All styles are inline along with a `display:none`, which this class overrides */
            div[class="hh-visible"] {
                display: block !important;
            }

            /* Center stuff */
            div[class="hh-center"] {
                text-align: center;
                width: 100% !important;
            }

            /* Constrain tables for small screens */
            table[class="hh-fluid"] {
                width: 100% !important;
            }

            /* Force images to resize to full width of their container */
            img[class="hh-fluid"],
            img[class="hh-force-col-center"] {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
            }
            /* And center these ones */
            img[class="hh-force-col-center"] {
                margin: auto !important;
            }

            /* Force table cells into rows */
            td[class="hh-force-col"],
            td[class="hh-force-col-center"] {
                display: block !important;
                width: 100% !important;
                clear: both;
            }
            /* And center these ones */
            td[class="hh-force-col-center"] {
                text-align: center !important;
            }

            /* Stack the previously floated images */
            img[class="col-3-img-l"],
            img[class="col-3-img-r"] {
                float: none !important;
                margin: 15px auto !important;
                text-align: center !important;
            }



        }

    </style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#f4f4f4" style="margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none;">
<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" bgcolor="#f4f4f4" id="bodyTable" style="border-collapse: collapse;table-layout: fixed;margin:0 auto;"><tr><td>

            <!-- Hidden Preheader Text : BEGIN -->
            {{--<div style="display:none; visibility:hidden; opacity:0; color:transparent; height:0; width:0;line-height:0; overflow:hidden;mso-hide: all;">--}}
                {{--Visually hidden preheader text.--}}
            {{--</div>--}}
            <!-- Hidden Preheader Text : END -->

            <!-- Logo Left, Nav Right, 100% Nav Bar : BEGIN -->
            <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center" bgcolor="#555555" style="color: white; background-color: #133457;font-weight:800;text-align: center;">
                <tr>
                    <td>
                        <table border="0" width="600" cellpadding="0" cellspacing="0" align="center" style="margin: auto;width:100%;" class="email-container">
                            <tr>
                                <td height="10" style="font-size: 0; line-height: 0;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="hh-force-col-center"  style="text-align: center;">
                                    <strong><h1>{{ $settings['company_name'] }}</h1></strong>
                                </td>
                            </tr>
							@if($settings['address_line_1']!=""||$settings['address_line_1']!=null)
							<tr>
                                <td class="hh-force-col-center"  style="text-align: center;">
                                    {{ $settings['address_line_1'] }}
                                </td>
                            </tr>
							@endif
							@if($settings['address_line_2']!=""||$settings['address_line_2']!=null)
							<tr>
                                <td class="hh-force-col-center"  style="text-align: center;">
                                    {{ $settings['address_line_2'] }}
                                </td>
                            </tr>
							@endif
							@if($settings['email_address']!=""||$settings['email_address']!=null)
							<tr>
                                <td class="hh-force-col-center"  style="text-align: center;">
                                    {{ $settings['email_address'] }}
                                </td>
                            </tr>
							@endif
							@if($settings['phone']!=""||$settings['phone']!=null)
							<tr>
                                <td class="hh-force-col-center"  style="text-align: center;">
                                    {{ $settings['phone'] }}
                                </td>
                            </tr>
							@endif
                            <tr>
                                <td height="10" style="font-size: 0; line-height: 0;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- Logo Left, Nav Right, 100% Nav Bar : END -->

            <!-- Logo Left Nav Right + Vertical Padding : BEGIN -->
            <table border="0" width="600" cellpadding="0" cellspacing="0" align="center" class="email-container" style="margin: auto;">
                <tr>
                    <td height="30" style="font-size: 0; line-height: 0;">&nbsp;</td>
                </tr>
                <tr>
                    <td class="force-col-center" valign="middle" style="padding: 20px 0;text-align: left;">
                        <div  border="0">
                            {{--<h1>EZPOS Sale Invoice Receipt</h1>--}}
                        </div>
                    </td>
                    <td class="force-col-center" valign="middle" style="padding: 20px 0;text-align: right;">

                    </td>
                </tr>
                <tr>
                    <td height="30" style="font-size: 0; line-height: 0;">&nbsp;</td>
                </tr>
            </table>
            <!-- Logo Left Nav Right + Vertical Padding : END -->

            <!-- Email Container : BEGIN -->
            <!-- This table wraps the whole body email within it's width (600px), sets the background color (white) and border (thin, gray, solid) -->
            <table border="0" width="600" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border: 1px solid #e5e5e5;margin: auto;" class="email-container">

                <!-- Single Fluid Image, No Crop : BEGIN -->
                <tr>
                    <td >
                        <img class="center" src="{{$message->embed(asset('img/logo.png'))}}" alt="{{ $settings['company_name'] }}" onerror="this.style.display='none'" height="100" width="100" border="0" >
                        {{--<img class="center" src="{{asset('img/logo.png')}}" alt="alt text" height="100" width="100" border="0"  >--}}
                    </td>
                </tr>
                <!-- Single Fluid Image, No Crop : END -->

                <!-- Full Width, Fluid Column : BEGIN -->
                <tr>
                    <td style="border-bottom: 1px solid #e5e5e5;">
                        <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center">
                            <tr>
                                <td  style="padding: 30px; font-family: sans-serif; font-size: 16px; line-height: 22px; color: #444444;">
                                    <h1 style="text-align: center">Invoice Statement</h1>
                                    <br><br>
                                Dear Valued Customer,<br><br>Kindly find attached copy of your charge account statement. Thanks in advance for your prompt remittance. <br><br>Cordially,<br>Accounts Receivables
                                    <br>{{$settings['company_name']}}<br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <!-- Full Width, Fluid Column : END -->




            </table>
            <!-- Email Container : END -->

            <!-- Footer : BEGIN -->
            <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center" class="email-container">
                <tr>
                    <td style="text-align: center;padding: 20px;font-family: sans-serif; font-size: 12px; line-height: 18px;color: #888888;">
                        {{$settings['company_name']}} &bull;  2017 BVI GRIMS LLC &bull; <span class="mobile_link">{{ $settings['address'] }}</span><br><br>
                    </td>
                </tr>
            </table>
            <!-- Footer : END -->

        </td></tr></table>
</body>
</html>