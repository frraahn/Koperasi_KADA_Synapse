<?php 
if(!session_id())
{
  session_start();
}

include 'headerapplicant.php';
include 'dbconnect.php';
include 'membershipstatusprocess.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Permohonan Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6686f6;
            --secondary: #318166;
            --accent: #f567a1;
            --dark-blue: #1255b8;
            --light-blue: #258de4;
            --purple: #5954bb;
            --gradient-1: linear-gradient(135deg, #6686f6, #5954bb);
            --gradient-2: linear-gradient(135deg, #258de4, #1255b8);
            --gradient-3: linear-gradient(135deg, #f567a1, #ff8fb8);
        }

        body {
            background-color: #f8f9fa;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png');
            background-size: cover;
          background-repeat: no-repeat;
          background-attachment: fixed;
          background-position: center;
        }

        .page-header {
            background: #6686f6;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .page-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            text-align: left;
            margin: 0;
            padding-left: 4.5rem;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .status-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(102, 134, 246, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(102, 134, 246, 0.2);
        }

        .status-text {
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .status-steps {
            max-width: 600px;
            margin: 0 auto;
            padding: 0.5rem;
        }

        .status-step {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin-right: 1rem;
            background-color: #dee2e6;
            transition: all 0.3s ease;
        }

        .status-step.active {
            background: linear-gradient(to right, rgba(15, 16, 19, 0.1), rgba(89, 84, 187, 0.1));
        }

        .status-step.active .status-indicator {
            background: var(--gradient-1);
            box-shadow: 0 0 10px rgba(102, 134, 246, 0.4);
        }

        .status-step.rejected {
            background: linear-gradient(to right, rgba(245, 103, 161, 0.1), rgba(255, 143, 184, 0.1));
        }

        .status-step.rejected .status-indicator {
            background: var(--gradient-3);
            box-shadow: 0 0 10px rgba(245, 103, 161, 0.4);
        }

        .status-step span {
            font-size: 0.9rem;
            color: #495057;
        }

        .rejection-message {
            color: var(--accent);
            background: linear-gradient(to right, rgba(245, 103, 161, 0.1), rgba(255, 143, 184, 0.1));
            padding: 0.75rem;
            border-radius: 10px;
            margin: 0.75rem 0;
            font-size: 0.85rem;
        }

        .past-applications {
            margin-top: 2rem;
        }

        .past-applications h3 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            text-align: center;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .application-card {
            background: linear-gradient(to right, white, #f8f9fa);
            border-radius: 15px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(102, 134, 246, 0.1);
        }

        .application-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(102, 134, 246, 0.15);
            border: 1px solid rgba(102, 134, 246, 0.2);
        }

        .application-card h4 {
            color: var(--primary);
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .application-card p {
            color: #6c757d;
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .status-badge.approved {
            background: linear-gradient(135deg, var(--secondary), #3da686);
            color: white;
        }

        .status-badge.rejected {
            background: var(--gradient-3);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 0.5rem;
            }

            .status-container {
                padding: 1rem;
            }

            .status-step {
                padding: 0.6rem;
            }
        }
        .btn {
            margin-top: auto; /* Push the button to the bottom */
            margin-left: auto;
            display: inline-block;
            padding: 4px 9px;
            background-color: var(--accent);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            width: 15%;
            height: auto;
        }

        .btn:hover {
            background-color: lightpink;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Status Permohonan Anggota</h2>
    </div>
    <div class="container">

            <div class="status-container">
                    <div class="status-step <?= $membershipStatus >= 1 ? 'active' : '' ?>">
                        <div class="status-indicator"></div>
                        <span>Permohonan anggota dihantar</span>
                    </div>
                    <div class="status-step <?= $membershipStatus >= 2 || $membershipStatus == 5 ? 'active' : ($membershipStatus == 4 ? 'rejected' : '') ?>">
                        <div class="status-indicator"></div>
                        <span>Menunggu kelulusan permohonan anggota</span>
                    </div>
                    <div class="status-step <?= $membershipStatus == 2 ? 'active' : ($membershipStatus == 3 ? 'rejected' : '') ?>">
                        <div class="status-indicator"></div>
                        <span>Permohonan pembiayaan anda telah diluluskan</span>
                    </div>
                    <div class="status-step <?= $membershipStatus == 2 ? 'active' : '' ?>">
                        <div class="status-indicator"></div>
                        <span>E-mel dihantar</span>
                    </div>
                </div>
            </div>
    </div>
</body>
</html>
