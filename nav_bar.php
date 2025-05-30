<?php
session_start();
$loggedIn = isset($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online ticket Booking</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav>
    <ul class="sidebar">
      <li onclick=hideSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="26" viewBox="0 96 960 960" width="26"><path d="m249 849-42-42 231-231-231-231 42-42 231 231 231-231 42 42-231 231 231 231-42 42-231-231-231 231Z"/></svg></a></li>
      <li><a href="#">Home</a></li>
      <li><a href="#">BOOK TICKET</a></li>
      <li><a href="#">Ticket Operation</a></li>
      
      <?php if ($loggedIn): ?>
          <li class="hideOnMobile"><a href="logout.php">Logout</a></li>
          <?php else: ?>
          <li class="hideOnMobile"><a href="login.php">Login</a></li>
      <?php endif; ?>

      <li><a href="#">About</a></li>
    </ul>
    <ul>
      <li class="hideOnMobile"><a href="nav_bar.php">Home</a></li>
      <li class="hideOnMobile"><a href="book_ticket.php">Book Ticket</a></li>
      <li class="hideOnMobile"><a href="ticket_operation.php">Ticket Operation</a></li>
      
      <?php if ($loggedIn): ?>
          <li class="hideOnMobile"><a href="logout.php">Logout</a></li>
          <?php else: ?>
          <li class="hideOnMobile"><a href="login.php">Login</a></li>
      <?php endif; ?>


      <li class="hideOnMobile"><a href="about.html">About</a></li>
      <li class="menu-button" onclick=showSidebar()><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="26" viewBox="0 96 960 960" width="26"><path d="M120 816v-60h720v60H120Zm0-210v-60h720v60H120Zm0-210v-60h720v60H120Z"/></svg></a></li>
    </ul>
  </nav>

  <script>
    function showSidebar(){
      const sidebar = document.querySelector('.sidebar')
      sidebar.style.display = 'flex'
    }
    function hideSidebar(){
      const sidebar = document.querySelector('.sidebar')
      sidebar.style.display = 'none'
    }
  </script>
</body>
</html>