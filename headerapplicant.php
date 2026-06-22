<!DOCTYPE html>
<html lang="en">
<head>
  <title>KOPERASI KADA</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    #fixed-navbar {
      background-color: #FFFFFF !important;
      z-index: 1100 !important;
      box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.5) !important;
      position: fixed !important;
      top: 0 !important;
      width: 100% !important;
      height: 60px !important;
      display: flex !important;
      align-items: center !important;
      line-height: 40px;
    }
    
    #fixed-navbar .container-fluid {
      display: flex !important;
      align-items: center !important;
      position: relative;
    }

    #fixed-navbar button, #fixed-navbar img {
      margin-right: 15px !important;
    }

    #fixed-navbar a, #fixed-navbar button {
      color: #E43D12 !important; /* Navbar text color */
      font-weight: bold !important;
    }

    /* Sidebar styling */
    .sidebar {
      position: fixed;
      top: 0;
      left: -250px;
      width: 250px;
      height: 100vh;
      background: #EBE9E1; /* Same as navbar */
      color: #343a40; /* Dark text for readability */
      transition: left 0.3s;
      padding-top: 60px;
      z-index: 1050;
      box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.5);
    }
    
    body {
      overflow-x: hidden;
      padding-top: 60px !important;
    }

    .sidebar.active {
      left: 0;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      padding: 10px;
      position: relative;
    }

    .sidebar ul li a {
      color: #343a40; /* Dark text for sidebar links */
      text-decoration: none;
      display: block;
      font-weight: bold;
      text-align: left; 
      width: 100%; 
    }

    .sidebar ul li a:hover {
      background: #D6536D; /* Slightly darker hover effect */
      color: white;
    }

    .sidebar ul .dropdown-menu {
      display: none;
      background: #FFA2B6; /* Softer dropdown background */
      padding-left: 15px;
    }

    .sidebar ul .dropdown-menu a {
      padding: 8px 10px;
      display: block;
      color: #343a40;
      text-align: left; 
      width: 100%; 
    }

    .sidebar ul .dropdown-menu a:hover {
      background: #E43D12; /* Highlight hover effect */
      color: white;
    }

    .sidebar ul .dropdown-toggle {
      cursor: pointer;
    }

    #menu-toggle {
      background: transparent !important;
      border: none !important;
      font-size: 24px !important;
      cursor: pointer !important;
      color: #E43D12 !important;
      margin-left: 0% !important;
    }

    #fixed-navbar img {
      height: 40px !important;
      margin-left: 94% !important;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg" id="fixed-navbar">
  <div class="container-fluid">
      <button id="menu-toggle">☰</button>
      <img src="img/logoKADA.jpeg" height="40" alt="KOPERASI KADA Logo">
    </div>
  </nav>

  <div class="sidebar" id="sidebar">
    <ul><br>
      <li><a href="applicantHome.php">Laman Utama</a></li>
      
      <li>
        <a class="dropdown-toggle">Permohonan Anggota </a>
        <ul class="dropdown-menu">
          <li><a href="applicant.php">Borang Keanggotaan</a></li>
          <li><a href="membershipstatus.php">Status Permohonan Keanggotaan</a></li>
        </ul>
      </li>

      <li>
        <a class="dropdown-toggle">Permohonan Pembiayaan </a>
        <ul class="dropdown-menu">
          <li><a href="pinjaman.php">Borang Pembiayaan</a></li>
          <li><a href="statuspinjaman.php">Status Permohonan Pembiayaan</a></li>
        </ul>
      </li>

      <li><a href="calculator.php">Kalkulator Bayaran Balik</a></li>
      <li><a href="transactioninfo.php">Rekod Pembayaran</a></li>
      
      <li>
        <a class="dropdown-toggle">Permohonan Berhenti </a>
        <ul class="dropdown-menu">
          <li><a href="berhenti.php">Borang Berhenti</a></li>
          <li><a href="statusberhenti.php">Status Permohonan Berhenti</a></li>
        </ul>
      </li>

      <li>
        <a class="dropdown-toggle">Akaun </a>
        <ul class="dropdown-menu">
          <li><a href="profile.php">Profil</a></li>
          <li><a href="logout.php">Log Keluar</a></li>
        </ul>
      </li>
    </ul>
  </div>

  <script>
    document.getElementById("menu-toggle").addEventListener("click", function() {
      document.getElementById("sidebar").classList.toggle("active");
    });

    // Toggle Dropdowns
    document.querySelectorAll(".dropdown-toggle").forEach(item => {
      item.addEventListener("click", function() {
        let dropdownMenu = this.nextElementSibling;
        dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
      });
    });
  </script>
</body>
</html>
