<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    
  </head>
  <body>
  
				<form action="{{ route('post') }}" method="POST" enctype="multipart/form-data">
					<input type="text" placeholder='token' name="token"><br>
					<input type="text" placeholder="text" name="text"><br>
					<input type="text" placeholder="latitude" name="latitude"><br>
					<input type="text" placeholder="longitude" name="longitude"><br>
					<input type="int" placeholder="anonymous" name="anonymous"><br>
					Image <input type="file" name="image" /><br/>
					<input type = "submit" value = "SHOUT" /><br>
					
				</form>
		</div>
		
  </body>
</html>