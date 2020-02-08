<?php
include "control.php";
$showcontent = new PageContent();
echo $showcontent->showPage('chatroom');

// Get the members to show which ones are online and which aren't.
$allmembers = new Member();
$members = $allmembers->getAllMembers('login_status desc');

// Get class for chatroom database handling.
$chatroom = new ChatRoom();
// Update login_status to 1 when user arrives on this page.
$chatroom->updateChatLoginStatus($username, 1);
// Load chat message history.
$allchatmessages = $chatroom->loadChatRoom();

// User class to update login_status.
$user = new User();

// Manually leaves the chat by clicking the Leave button.
if(isset($_POST['action'])) 
{
  $action = $_POST['action'];
  if ($action === 'leave') {
    $user->updateChatLoginStatus($username, 0);
  }
}
$wsdomain_array = explode("//", $domain);
$wsdomain = $wsdomain_array[1];
?>

<div class="container ja-chat-container">
  <h1 class="ja-chat-title ja-bottompadding"><?php echo $sitename ?> Chat</h1>
  <div class="ja-sidebar ja-small-font">
      <div class="ja-sidebar-thisuser">
        <div>Logged in as: <?php echo $firstname . " " . $lastname . " (" . $username . ")"; ?></div>
        <input type="button" class="btn btn-warning" id="leave-chat" name="leave-chat" value="Leave">
      </div>
      <div class="ja-sidebar-headings">
        <div>User</div>
        <div>Last Logged In</div>
      </div>   
      <?php
          foreach ($members as $member) {
            $dotcolor = "color: #f00";
            $fontcolor = "color: #c0c0c0";
            $online = "Offline";
            if ($member['login_status'] === "1") {
              $dotcolor = "color: #0f0";
              $fontcolor = "color: #fff";
              $online = "Online";
            }
            // show the time if the user last logged in today, otherwise show the date only.
            $lastlogindate = strtotime($member['lastlogin']);
            $onedayago = strtotime('-1 day');
            if ($member['lastlogin'] == null) {
              $showdate = "N/A";
            } elseif ($lastlogindate < $onedayago) {
              $showdate = date("M-d-Y", strtotime($member['lastlogin']));
            } else {
              $showdate = date("g:i A", strtotime($member['lastlogin']));
            }
            echo "<div class=\"ja-sidebar-oneuser\">";
              echo "<div>" . $allmembers->getGravatar($member['username'], $member['email']) . "</div>";
              echo "<div>" . $member['username'] . "<br />";
              echo "<span class=\"fas fa-circle ja-rightpadding1\" style=\"" . $dotcolor . "\"></span>
                    <span style=\"" . $fontcolor . "\">" . $online . "</span></div>";
              echo "<div>" . $showdate . "</div>";
            echo "</div>";
          }
        ?>           
  </div>
	<div class="ja-chatbox ja-small-font">
    <div id="ja-chat-messages">
      <?php
        foreach($allchatmessages as $chatmessage) {
          // show the time if the message was sent today, otherwise show the date and time.
          $messagedatestr = strtotime($chatmessage['created_on']);
          $onedayagostr = strtotime('-1 day');
          if ($messagedatestr < $onedayagostr) {
            $messagedate = date("M-d-Y g:i A", strtotime($chatmessage['created_on']));
          } else {
            $messagedate = date("g:i A", strtotime($chatmessage['created_on']));
          }
          echo "<div class=\"ja-chat-onemessage\">";
            echo "<div>" . $allmembers->getGravatar($chatmessage['username'], $chatmessage['email']) . "</div>";
            echo "<div>" . $chatmessage['username'] . "<br />" . $chatmessage['msg'] . "</div>";
            echo "<div>" . $messagedate . "</div>";
          echo "</div>";
        }
      ?>
    </div>
  </div>
  <form class="ja-chatform" method="post" action="">
    <div class="form-group">
      <textarea class="form-control" id="msg" name="msg" placeholder="Enter Message"></textarea>
    </div>
    <div class="form-group">
      <input type="button" value="Send" class="btn btn-success btn-block" id="send" name="send">
    </div>
  </form>
</div>
<script type="text/javascript">
  $(document).ready(function() {

    // Scroll chat box to the end.
    $("#ja-chat-messages").animate({ scrollTop: $('#ja-chat-messages').prop("scrollHeight")}, 1000);
    // non-animated: $('#ja-chat-messages').scrollTop($('#ja-chat-messages')[0].scrollHeight);

    // Refresh PHP session so user is not automatically logged out while they have the chat open.
    setInterval(function() { $.post('refreshchatsession.php'); }, 600000);

    let wsdomain = '<?php echo $wsdomain ?>';
    let conn = new WebSocket("ws://" + wsdomain + ":8080");

    conn.onopen = function(e) {
      console.log("Connection established!");
    }

    // onmessage received this is what happens:
    conn.onmessage = function(e) {
      let data = JSON.parse(e.data);
      // console.log(data);
      let row = `<div class="ja-chat-onemessage">
          <div>${data.username}</div>
          <div>${data.text}</div>
          <div>${data.dt}</div>
      </div>
      `;
      // Add the new message row to the chat box.
      $('#ja-chat-messages').append(row); 
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
      let username = '<?php echo $username ?>';
      console.log(username);
      $.post('userleavechat.php');

      // $.ajax({
      //   // url: "", // Leave empty if we are posting to the same page.
      //   method: "post",
      //   data: `action=leave`
      // }).done(function(result) {
      //   // Send message to all users that this user has left the chat.
      //   let data = {
      //   'username': username,
      //   'text': `${username} has left the chat.`
      //   };
        // conn.send(JSON.stringify(data) );
        // conn.close(); // Calls conn.onclose above.
        // console.log(result);
      // });
    });

  });
</script>