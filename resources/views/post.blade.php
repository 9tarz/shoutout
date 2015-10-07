<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    
  </head>
  <body>
  
				<form action="{{ route('post') }}" method="POST">
					<input type="text" placeholder='token' name="token"><br>
					<input type="text" placeholder="text" name="text"><br>
					<input type="text" placeholder="latitude" name="latitude"><br>
					<input type="text" placeholder="longitude" name="longitude"><br>
					<input type="int" placeholder="anonymous" name="anonymous"><br>
					
					<input type = "submit" value = "SHOUT" /><br>
					
				</form>
		</div>
		
  </body>
</html>