<!DOCTYPE html>
<html lang="en">
<head>
  <title>KOPERASI KADA</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    /* Navbar styling */
    .navbar {
      background-color: #FFFFFF !important; /* Navbar and Sidebar background */
      z-index: 1100; /* Ensure navbar is above other content */
      box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.5);
    }
    
    .navbar .container-fluid {
      display: flex;
      align-items: center;
    }

    .navbar button, .navbar img {
      margin-right: 15px;
    }

    .navbar a, .navbar button {
      color: #E43D12 !important; /* Navbar text color */
      font-weight: bold;
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
      overflow-x: hidden; /* Prevent horizontal scroll due to sidebar */
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
      background: transparent;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: #E43D12; /* Button color */
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <button id="menu-toggle">☰</button>
      <img src="img/logoKADA.jpeg" height="40" alt="KOPERASI KADA Logo">
    </div>
  </nav>

  <div class="sidebar" id="sidebar">
    <ul><br>
      <li><a href="alk.php">Laman Utama</a></li>

      <li><a class="nav-link" href="membershipList.php">Senarai Permohonan Anggota</a></li>
      <li><a class="nav-link" href="loanList.php">Senarai Pemohonan Pembiayaan</a></li>
      <li><a class="nav-link" href="terminateList.php">Senarai Pemohonan Berhenti</a></li>
      <li><a class="nav-link" href="historyMembership.php">Sejarah Permohonan Anggota</a></li>
      <li><a class="nav-link" href="historyLoan.php">Sejarah Pemohonan Pembiayaan</a></li>


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
