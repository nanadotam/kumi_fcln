<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kumi Landing Page</title>
  <link rel="stylesheet" href="../assests/css/styles.css">
  <link rel="stylesheet" href="../assests/css/landing_styles.css">
</head>
<body>
  <!-- Navbar -->
  <header>
    <nav class="navbar">
      <div class="logo">
        <img src="../assests/images/KUMI_logo.svg" alt="Kumi Logo">
      </div>
      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#team">Meet the Team</a></li>
        <li><a href="#waitlist">Join the Waitlist</a></li>
      </ul>
      <div class="auth-buttons">
        <a href="../view/login.php" class="btn btn-login">Login</a>
        <a href="../view/register.php" class="btn btn-register">Register</a>
      </div>
    </nav>
  </header>

  <!-- Home Section -->
  <section id="home" class="section">
    <div class="fullscreen-container">
        <video autoplay muted loop class="fullscreen-video">
            <source src="../assests/vid/KUMI_home.mp4" type="video/mp4">
            Your browser does not support the video tag.
          </video>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="section">
    <h2>About Us</h2>
    <p>Kumi is a platform designed to foster learning, creativity, and collaboration through engaging activities and interactions.</p>
  </section>

  <!-- Meet the Team Section -->
  <section id="team" class="section">
    <h2>Meet the Team</h2>
    <div class="team-cards">
      <div class="card">
        <img src = "../assests/images/nana.jpeg"> 
        <h3>Nana Kwaku Amoako</h3>
        <p>Front End Developer</p>
      </div>
      <div class="card">
        <img src = "../assests/images/lady.jpeg"> 
        <h3>Lady-M. Hagan</h3>
        <p>Product Manager</p>
      </div>
      <div class="card">
        <img src = "../assests/images/caleb.jpeg"> 
        <h3>Caleb O. Arthur</h3>
        <p>Backend Developer</p>
      </div>
      <div class="card">
        <img src = "../assests/images/frances.jpg"> 
        <h3>Frances S. Fiahagbe</h3>
        <p>Backend Developer</p>
      </div>
    </div>
  </section>

  <!-- Join the Waitlist Section -->
  <section id="waitlist" class="section">
    <h2>Join the Waitlist</h2>
    <a href="../view/waitlist.html" class="btn btn-primary">Join Now</a>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2024 Kumi - All rights reserved.</p>
  </footer>
</body>
</html>