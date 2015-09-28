<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    
  </head>
  <body>
  
				<form action="{{ route('register') }}" method="POST">
					<input type="text" placeholder='username' name="username"><br>
					<input type="text" placeholder="password" name="password"><br>
					<input type="text" placeholder="firstname" name="first_name"><br>
					<input type="text" placeholder="lastname" name="last_name"><br>
					<input type="text" placeholder="email" name="email"><br>
					
					<input type = "submit" value = "LOGIN" /><br>
					
				</form>
		</div>
		
  </body>
</html>
