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
  $action = $_POST['action'];
  if ($action === 'leave') {
    $user->updateChatLoginStatus($username, 0);
  }
}
?>

<div class="container ja-chat-container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">		
			<h1 class="ja-bottompadding"><?php echo $sitename ?> Chat</h1>
    </div>
  </div>	
  <div class="row">
		<div class="col-md-4">			
      <table class="table ja-small-font">
        <thead>
          <tr>
            <td align="left">
              <div>Logged in as: <?php echo $firstname . " " . $lastname ?></div>
              <div><?php echo $email ?></div>
            </td>
            <td align="right" colspan="2">
              <input type="button" class="btn btn-warning" id="leave-chat" 
              name="leave-chat" value="Leave">
            </td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th colspan="3">Users</th>
          </tr>
        <?php
          foreach ($members as $member) {
            $color = "color: #f00";
            $online = "Offline";
            if ($member['login_status'] === "1") {
              $color = "color: #0f0";
              $online = "Online";
            }
            $member_fullname = $member['firstname'] . " " . $member['lastname'];
            
            // ADD GRAVATAR (need user email) - change to uploaded photos later.

            echo "<tr><td>" . $member_fullname . "</td>";
            echo "<td><span class=\"fas fa-circle\" style=\"" . $color . "\"></span>" . $online . "</td>";
            echo "<td>" . date("M-d-Y h:i:s A", strtotime($member['lastlogin'])) . "</td></tr>";
          }
        ?>       
        </tbody>
      </table>
		</div>
    <div class="col-md-8">
      <div id="chat-messages">
        <table id="chats" class="table ja-small-font">
          <thead>
          </thead>
          <tbody>
            <?php
              foreach($allchatmessages as $chatmessage) {
                echo '<tr>
                        <td valign="top" align="left">
                          <div>' . $chatmessage['username'] . '</div>
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
<script type="text/javascript">
  $(document).ready(function() {

    // Scroll chat box to the end.
    $("#chat-messages").animate({ scrollTop: $('#chat-messages').prop("scrollHeight")}, 1000);
    // non-animated: $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

    let conn = new WebSocket('ws://collectorsscave.phpsitescripts.com:8080');

    conn.onopen = function(e) {
      console.log("Connection established!");
    }

    // onmessage received this is what happens:
    conn.onmessage = function(e) {
      let data = JSON.parse(e.data);
      // console.log(data);
      let row = `<tr>
        <td valign="top" align="left">
          <div>${data.username}</div>
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
      let username = "<?php echo $username ?>";
      let msg = $('#msg').val();
      let data = {
        'username': username,
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
        'username': username,
        'text': `${username} has left the chat.`
        };
        conn.send(JSON.stringify(data) );
        conn.close(); // Calls conn.onclose above.
        // console.log(result);
      });
    });

  });
</script>