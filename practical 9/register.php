<!DOCTYPE html> 

<html> 

<head> 

    <title>User Registration</title> 

    <link rel="stylesheet" href="style.css"> 

</head> 
<body> 
<div class="container"> 
    <h2>User Registration</h2> 
    <form action="process_registration.php" method="POST"> 
        <label>Username:</label>
        <input type="text"name="username" required minlength="3" maxlength="50">
        <label>Email:</label> 
        <input type="email"name="email" required > 
<label>Password:</label> 
        <input type="password"name="password"required minlength="6" >
        <label>Confirm Password:</label> 
        <input  

            type="password"  

            name="confirm_password"  

            required 

            minlength="6" 

        > 

 

        <button type="submit">Register</button> 

 

    </form> 

 

</div> 

 

</body> 

</html> 