<?php
include "control.php";
$showcontent = new PageContent();
echo $showcontent->showPage('Members Area Chatroom Page');
# Get the members to show which ones are online and which aren't.
$allmembers = new Member();
$members = $allmembers->getAllMembers();
?>

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
		
			<h1 class="ja-bottompadding"><?php echo $title ?> Chat</h1>
			


			<div class="ja-bottompadding"></div>

		</div>
	</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    let conn = new WebSocket('ws://localhost:8080');
    conn.onopen = function(e) {
      console.log("Connection established!");
    }
    conn.onmessage = function(e) {
      console.log(e.data);
    }
    $('#send').click(function() {
      let userId = $('#userId').val();
      let msg = $('#msg').val();
      let data = {
        userId,
        msg
      };
      conn.send(JSON.stringify(data) );
    });
  });
</script>