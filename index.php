<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>COPRAR EDITOR</title>
  </head>
  <body>
    <?php require_once 'process.php'; ?>
    <?php 
    
      if(isset($_SESSION['data'])){
        $data = $_SESSION['data'];
        $messages = $data['message'];
        $details = $data['details'];
        $errors = $data['errors'];
      }
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand" href="/">COPRAR EDITOR</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="/">Home</a>
            </li>
        </div>
      </div>
    </nav>
    <div class="container pt-3">
    	<div class="row">
    		<div class="col-6 border-left rounded-left">
    			<form action="process.php" method="POST" class="form" enctype="multipart/form-data">
    				<div class="mb-3">
                        <label for="receiver_code">Receiver Code:</label>
                        <input type="text" value="RECEIVER" class="form-control" name="receiver_code"/>
                    </div>
                    <div class="mb-3">
                        <label for="callsign_code">Callsige Code:</label>
                        <input type="text" value="XXXXX" class="form-control" name="callsign_code"/>
                    </div>
                    <div class="mb-3">
                        <label for="excel_file">Export booking excell file:</label>
                        <input type="file" class="form-control" name="excel_file" required/>
                    </div>
                    <button class="btn btn-primary" type="submit" name="submit">Generate</button>
    			</form>
          <?php if(isset($errors) && $errors != []) {?>
            <ul class="list-group">
              <?php foreach($errors as $e){ ?>
                <li class="list-group-item list-group-danger"><?php print($e); ?></li>
              <?php } ?>
            </ul>
          <?php } ?>
    		</div>
    		<div class="col-6 border-right rounded-right">
    			<label for="output" class="form-label">Output</label>
    			<textarea
    			 name="output"
    			 id="output"
    			 class="form-control"
    			 placeholder="Please upload the excell file to view the output..."
           rows="30"
    			><?php 
              if (isset($details) && $details != []) {
                $text = $details['header'];
                $body = $details['body'];
                $footer = $details['footer'];
                
                foreach($body as $item){
                  $text .= implode('',$item);
                }
                $text .= implode('',$footer);
                $text = Trim($text);
                print(trim($text));
              }
            ?>
          </textarea>
    		</div>
    	</div>
    </div>
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
  </body>
</html>