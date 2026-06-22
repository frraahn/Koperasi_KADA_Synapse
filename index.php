<?php include 'headermain.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Index</title>
  <style>
  body {
      margin: 0;
      font-family: Arial, sans-serif;
  }

  /* Background image setup */
  .background {
      background-image: url('img/img.png');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      height: 100%;
      width: 100%;
      position: relative;
  }

  /* Override Bootstrap navbar styles while keeping functionality */
  .navbar-container {
      background-color: rgba(0, 0, 0, 0.8) !important;
      border-radius: 10px 10px 0 0 !important;
      width: 80% !important;
      margin: 0 auto !important;
  }

  /* Ensure navbar links maintain your custom style */
  .navbar-container .navbar-nav .nav-link {
      color: #fff !important;
      text-decoration: none !important;
      margin: 0 10px !important;
      font-size: 14px !important;
  }

  .navbar-container .navbar-nav .nav-link:hover {
      text-decoration: underline !important;
  }

  /* Rest of your custom styles */
  .scroll-container {
      background-color: rgba(255, 255, 255, 0.8);
      color: #000;
      width: 80%;
      margin: auto;
      height: 70vh;
      overflow-y: scroll;
      padding: 20px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
  }

  .scroll-container img {
    width: 100%; /* Full-width images inside the container */
    margin-bottom: 20px;
  }

  .scroll-container h3 {
    font-size: 18px;
    margin-bottom: 10px;
  }

  .hor{
    display: flex;
    justify-content: space-between; /* Spread the sections across the row */
    width: 100%;
    margin-bottom: 20px;
  }

  .horizontal-line-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    width:100%; /* Add spacing below each section */
  }

  .horizontal-line-container1 {
  flex: 1; /* Allow sections to take equal width */
  text-align: center;
  }

  .horizontal-line {
    flex-grow: 1;
    height: 2px; /* Adjust thickness of the line */
    background-color: black; /* Line color */
    margin: 0 20px; /* Add space between the lines and the text */
  }

  .horizontal-line-text {
    font-size: 18px; /* Adjust font size */
    font-weight: bold; /* Make the text bold */
    color: black; /* Text color */
    text-align: center;
  }

  .section-content-wrapper {
  display: flex;
  justify-content: space-between;
  width: 100%;
  margin-top: 20px;
  }

  .section-content {
    flex: 1; /* Allow section content to take equal width */
    padding: 15px;
    border-radius: 5px;
    background-color: rgba(255, 255, 255, 0.4);
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    margin-left: 10px;
    margin-right: 10px;
  }

  .section-content ul {
    padding-left: 20px;
    margin: 0;
  }

  .section-content ul li {
    margin-bottom: 10px;
  }

  .section-content a {
    color: #007BFF; /* Bootstrap blue */
    text-decoration: none;
  }

  .section-content a:hover {
    text-decoration: underline;
  }

  /* Footer section */
    .footer {
    background-color: rgba(0, 0, 0, 0.8); /* Match navbar color */
    color: white;
    text-align: center;
    padding: 10px 20px; /* Adjust padding for content */
    border-top: none; /* Remove border for consistent design */
    border-radius: 0 0 10px 10px; /* Rounded corners for a unified look */
    width: 80%; /* Match navbar and scroll-container width */
    margin: auto; /* Center horizontally */
  }

  .image-row {
    display: flex; /* Align items in one row */
    justify-content: flex-end; /* Align items to the right */
    gap: 10px; /* Add space between images */
  }

  .image-row img {
    width: 50px; /* Adjust to your desired width */
    height: auto; /* Maintain aspect ratio */
  }

  .content-container {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 80%;
    margin: auto;
  }

</style>
</head>
<body>
<div class="background">

  <!-- Scrollable Black Container -->
  <br><br><br><br><br>
  <div class="navbar-container">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Laman Utama</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Info KADA</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Umum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Petani/Usahawan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Warga KADA</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
  </div>
  <div class="scroll-container">
    
        <div>
            <div class="image-row">
              <a href="#">
                <img src="img/faq.png" alt="FAQ">
              </a>
              <a href="#">
                <img src="img/img3.png" alt="Hubungi Kami">
              </a>
              <a href="#">
                <img src="img/img4.png" alt="Aduan dan Maklum Balas">
              </a>
              <a href="#">
                <img src="img/img5.png" alt="Peta Laman">
              </a>
            </div>

           <img src="img/img2.png" alt="Sample Content" class="img-fluid"> 

          <br><br><br><br><br><br><br><br><br><br>
          <!-- Berita Section -->
          <div class="hor">
          <div class="horizontal-line-container">
            <div class="horizontal-line"></div>
            <div class="horizontal-line-text">Berita</div>
            <div class="horizontal-line"></div>
          </div>

          <!-- Info Tani Section -->
          <div class="horizontal-line-container">
            <div class="horizontal-line"></div>
            <div class="horizontal-line-text">Info Tani</div>
            <div class="horizontal-line"></div>
          </div>

          <!-- Pengumuman Section -->
          <div class="horizontal-line-container">
            <div class="horizontal-line"></div>
            <div class="horizontal-line-text">Pengumuman</div>
            <div class="horizontal-line"></div>
          </div>
        </div>

        <!-- Section Content for each one -->
        <div class="section-content-wrapper">
          <!-- Berita Content -->
          <div class="section-content">
            <h3>Latest News</h3>
            <ul>
              <li><a href="#">Berita 1</a></li>
              <li><a href="#">Berita 2</a></li>
              <li><a href="#">Berita 3</a></li>
            </ul>
          </div>

          <!-- Info Tani Content -->
          <div class="section-content">
            <h3>Latest Info Tani</h3>
            <ul>
              <li><a href="#">Info Tani 1</a></li>
              <li><a href="#">Info Tani 2</a></li>
              <li><a href="#">Info Tani 3</a></li>
            </ul>
          </div>

          <!-- Pengumuman Content -->
          <div class="section-content">
            <h3>Latest Announcement</h3>
            <ul>
              <li><a href="#">Pengumuman 1</a></li>
              <li><a href="#">Pengumuman 2</a></li>
              <li><a href="#">Pengumuman 3</a></li>
            </ul>
          </div>
        </div>

        
        </div>
</div>

        <footer class="footer">
                <p>© Sesuai dipapar menggunakan IE Versi 7.0 & ke atas...</p>
        </footer>
        <br><br>

</body>
</html>
