<?php
include "control.php";
$showcontent = new PageContent();
echo $showcontent->showPage('Members Area Chatroom Page');

// Get the members to show which ones are online and which aren't.
$allmembers = new Member();
$members = $allmembers->getAllMembers('login_status desc');

// Get class for chatroom database handling.
$chatroom = new ChatRoom();
$allchatmessages = $chatroom->loadChatRoom();

// User class to update login_status.
$user = new User();
// Update login_status to 1 when user arrives on this page.
$user->updateChatLoginStatus($username, 1);
// Manually leaves the chat by clicking the Leave button.
if(isset($_POST['action'])) 
{
  if ($action === 'leave') {
    $user->updateChatLoginStatus($username, 0);
    @header('Location:members.php');
  }
}
?>

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">		
			<h1 class="ja-bottompadding"><?php echo $title ?> Chat</h1>
    </div>
  </div>	
  <div class="row">
		<div class="col-md-4">			
      <table class="table table-striped">
        <thead>
          <tr>
            <td>
              <div><?php echo $firstname . " " . $lastname ?></div>
              <div><?php echo $email ?></div>
            </td>
            <td align="right" colspan="2">
              <input type="button" class="btn btn-warning" id="leave-chat" 
              name="leave-chat" value="Leave">
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
            echo "<td>" . date("M-d-Y h:i:s A", strtotime($member['lastlogin'])) . "</td></tr>";
          }
        ?>       
        </tbody>
      </table>
		</div>
    <div class="col-md-8">
      <div id="messages">
        <table id="chats" class="table table-striped">
          <thead>
            <tr>
              <th colspan="4" scope="col"><strong>Chat Room</strong></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach($allchatmessages as $chatmessage) {
                echo '<tr>
                  <td valign="top">
                    <div><strong>' . $chatmessage['username'] . '</strong></div>
                    <div>' . $chatmessage['msg'] . '</div>
                  </td>
                  <td valign="top" align="right">' . date("M-d-Y h:i:s A", strtotime($chatmessage['created_on'])) . '</td>
                </tr>';
              }
            ?>
            <!-- This is where the new messages will appear!  -->
          </tbody>
        </table>           
      </div>
      <form id="chat-room-frm" method="post" action="">
          <div class="form-group">
            <textarea class="form-control" id="msg" name="msg" placeholder="Enter Message"></textarea>
          </div>
          <div class="form-group">
            <input type="button" value="Send" class="btn btn-success btn-block" id="send" name="send">
          </div>
      </form>
    </div>
	</div>
</div>
<div class="ja-bottompadding"></div>
<script type="text/javascript">
  $(document).ready(function() {
    let conn = new WebSocket('ws://localhost:8080');

    conn.onopen = function(e) {
      console.log("Connection established!");
    }

    // onmessage received this is what happens:
    conn.onmessage = function(e) {
      console.log(e.data);
      let data = JSON.parse(e.data);
      let row = `<tr>
        <td valign="top">
          <div><strong>${data.user}</strong></div>
          <div>${data.text}</div>
        </td>
        <td valign="top" align="right">${data.dt}</td>
      </tr>
      `;
      // Add the new message row to the chat box.
      $('#chats > tbody').append(row); 
    }

    conn.onclose = function(e) {
      console.log("Connection Closed!");
    }

    $('#send').click(function() {
      // let userId = $('#userId').val();
      let username = "<?php echo $username ?>";
      let msg = $('#msg').val();
      let data = {
        'user': username,
        'text': msg
      };
      conn.send(JSON.stringify(data) );
      $('#msg').val(''); // reset the form field to be empty.
    });

    $('#leave-chat').click(function() {
      let username = "<?php echo $username ?>";
      $.ajax({
        // url: "", // Leave empty if we are posting to the same page.
        method: "post",
        data: `action=leave`
      }).done(function(result) {
        // Send message to all users that this user has left the chat.
        let data = {
        'user': username,
        'text': username . " has left the chat."
        };
        conn.send(JSON.stringify(data) );
        conn.close(); // Calls conn.onclose above.
        // console.log(result);
      });
    });

  });
</script>