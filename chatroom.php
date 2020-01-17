<?php
include "control.php";
$showcontent = new PageContent();
echo $showcontent->showPage('Members Area Chatroom Page');
# Get the members to show which ones are online and which aren't.
$allmembers = new Member();
$members = $allmembers->getAllMembers('login_status desc');
?>

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
		
			<h1 class="ja-bottompadding"><?php echo $title ?> Chat</h1>
			
      <table class="table table-striped">
        <thead>
          <tr>
            <td>
              <div><?php echo $firstname . " " . $lastname ?></div>
              <div><?php echo $email ?></div>
            </td>
          </tr>
          <tr>
            <th>Users</th>
          </tr>
        </thead>
        <tbody>
        <?php
          foreach ($members as $member) {
            $color = "color: #f00";
            if ($member['login_status'] === 1) {
              $color = "color: #00f";
            }
            $member_fullname = $member['firstname'] . " " . $member['lastname'];
            echo "<tr><td>" . $member_fullname . "</td>";
            echo "<td><span class=\"fas fa-circle\" style=\"" . $color . "\"></span></td>";
            echo "<td>" . $member['lastlogin'] . "</td></tr>";
          }
        ?>       
        </tbody>
      </table>

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