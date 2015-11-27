<html>
  <h3>Form</h3>
  <form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
    Image <input type="file" name="image" /><br/>
    <input type="submit" value="Upload to Imgur" />
  </form>
</html>