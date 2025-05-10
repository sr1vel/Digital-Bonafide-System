<!DOCTYPE html>
<html>
<head>
    <style>
        /* Basic styling for the navigation bar */
        .navbar {
            overflow: hidden;
            background-color: #092a47;
            padding: 15px;
        }
        
        /* Style the links inside the navigation bar */
        .navbar a {
            float: right;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        
        /* Change the color of links on hover */
        .navbar a:hover {
            background-color: #ffff;
            color: black;
        }
        
        /* Float the logout button to the right */
        .logout {
            float: right;
        }
        
        /* Style for images */
        .icon {
            width: 150px; /* Adjust image width */
            height: 150px; /* Adjust image height */
            margin-right: 90px;
            margin-top: 150px;
            margin-left: 40px;
        }
        
        /* Style for button container */
        .button-container {
            display: flex;
            /* Center align buttons horizontally */
            margin-top: 10px;
            margin-left:30px ;
        }
        
        /* Style for buttons */
        .button {
            padding: 20px 30px;
            background-color: #ffff;
            color: black;
            border: none;
            cursor: pointer;
            margin: 0 10px;
            border-radius: 25px;
        }
        
        .button:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<div class="navbar">
    
    <a href="adminafterlogin.php">Home</a>
</div>
<!-- Images and buttons in the body -->
<div style="padding: 10px; display: flex; justify-content: center;">
    <div class="image-container">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpZALc9WM4MAPex7KP-3QuxYIrsnykM_OxaA&s" alt="Envelope" class="icon">
        <div class="button-container">
            <button class="button" onclick="window.location.href='totalreport.php'">TOTAL REPORT</button>
        </div>
    </div>
    
    <div class="image-container">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpZALc9WM4MAPex7KP-3QuxYIrsnykM_OxaA&s" alt="Bell" class="icon">
        <div class="button-container">
            <button class="button"onclick="window.location.href='ugreport.php'">UG REPORT</button>
        </div>
    </div>
    
    <div class="image-container">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpZALc9WM4MAPex7KP-3QuxYIrsnykM_OxaA&s" alt="User" class="icon">
        <div class="button-container">
            <button class="button" onclick="window.location.href='pgreport.php'">PG REPORT</button>
        </div>
    </div>
    
    <div class="image-container">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpZALc9WM4MAPex7KP-3QuxYIrsnykM_OxaA&s" alt="Settings" class="icon">
        <div class="button-container">
            <button class="button" onclick="window.location.href='passedreport.php'">PASSED REPORT</button>
        </div>
    </div>
</div>

</body>
</html>