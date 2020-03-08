<?php
include "control.php";
$showcontent = new PageContent();
echo $showcontent->showPage('chatroom');

// Get the members to show which ones are online and which aren't.
$allmembers = new Member();
$members = $allmembers->getAllMembers('login_status desc');

// Get class for chatroom database handling.
$chatroom = new ChatRoom();
// Load chat message history.
const MESSAGE_LIMIT_PER_SCROLL = 50;
$allchatmessages_array = $chatroom->loadChatRoom(0, MESSAGE_LIMIT_PER_SCROLL);
$allchatmessages = array_reverse($allchatmessages_array);

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
      $dotclass = "onlinestatus-dot-red";
      $wordclass = "onlinestatus-word-grey";
      $userclass = "onlinestatus-background-dark";
      $online = "Offline";
      if ($member['login_status'] === "1") {
        $dotclass = "onlinestatus-dot-green";
        $fontcolor = "color: #fff";
        $wordclass = "onlinestatus-word-white";
        $userclass = "onlinestatus-background-light";
        $online = "Online";
      }
      // show the time if the user last logged in today, otherwise show the date only.
      $lastlogindate = strtotime($member['lastlogin']);
      $onedayago = strtotime('-1 day');
      if ($member['lastlogin'] == null) {
        $showdate = "N/A";
      } elseif ($lastlogindate < $onedayago) {
        $showdate = date("M d, Y", strtotime($member['lastlogin']));
      } else {
        $showdate = date("g:i A", strtotime($member['lastlogin']));
      }
      echo "<div id=\"" . $member['username'] . "\" class=\"ja-sidebar-oneuser " . $userclass . "\">";
      echo "<div>" . $allmembers->getGravatar($member['username'], $member['email']) . "</div>";
      echo "<div>" . $member['username'] . "<br />";
      echo "<span id=\"" . $member['username'] . "-dot\" class=\"fas fa-circle ja-rightpadding1 " . $dotclass . "\"></span>
                    <span id=\"" . $member['username'] . "-word\" class=\"" . $wordclass . "\">" . $online . "</span></div>";
      echo "<div>" . $showdate . "</div>";
      echo "</div>";
    }
    ?>
  </div>
  <div class="ja-chatbox ja-small-font">
    <div id="ja-chat-messages">
      <div id="loader"><img src='images/loader.gif' alt="Loading..."></div>
      <?php
      foreach ($allchatmessages as $chatmessage) {
        // show the time if the message was sent today, otherwise show the date and time.
        $messagedatestr = strtotime($chatmessage['created_on']);
        $onedayagostr = strtotime('-1 day');
        if ($messagedatestr < $onedayagostr) {
          $messagedate = date("M d, Y g:i A", strtotime($chatmessage['created_on']));
        } else {
          $messagedate = date("g:i A", strtotime($chatmessage['created_on']));
        }
        if (
          $chatmessage['msg'] == $chatmessage['username'] . ' has left the chat' ||
          $chatmessage['msg'] == $chatmessage['username'] . ' has joined the chat'
        ) {
          echo "<div class=\"ja-chat-onemessage-chatstatus\">";
          echo "<div>" . $chatmessage['msg'] . "</div>";
          echo "<div>" . $messagedate . "</div>";
          echo "</div>";
        } else {
          echo "<div class=\"ja-chat-onemessage\">";
          echo "<div>" . $allmembers->getGravatar($chatmessage['username'], $chatmessage['email']) . "</div>";
          echo "<div>" . $chatmessage['username'] . "<br />" . $chatmessage['msg'] . "</div>";
          echo "<div>" . $messagedate . "</div>";
          echo "</div>";
        }
      }
      ?>
    </div>
  </div>
  <form class="ja-chatform">
    <div id="progress-bar"></div>
    <div class="input-group">
      <input type="text" id="msg" name="msg" class="form-control text_element_width" placeholder="Enter Message" required minlength="1" maxlength="500">
      <div class="input-group-btn">
        <label for="chatImageInput" class="btn btn-primary btn-block">Add Images</label>
        <div class="">
          <input type="file" value="Upload" id="chatImageInput" name="chatImageInput[]" multiple accept="image/jpeg, image/png, image/gif">
        </div>
      </div>
    </div>
    <div id="chatErrorMessageDiv" class="form-group">
      <div id="chatErrorMessage" class="alert alert-danger ja-small-font"></div>
    </div>
    <div class="form-group">
      <div id="previewImages"></div>
    </div>
    <div class="form-group">
      <input type="submit" value="Send" class="btn btn-success btn-block" id="send" name="send">
    </div>
  </form>
</div>

<div class="modal fade" id="enlargedImageModal" tabindex="-1" role="dialog" aria-labelledby="enlargedImageModal" aria-hidden="true">
  <div class="modal-dialog ja-modal-width">
    <div class="modal-content ja-modal">
      <div class="modal-body">
        <img id="modalImage" src="images/loader.gif" alt="Enlarged image from chat message">
      </div>
      <div class="modal-footer">
        <button class="btn btn-lg btn-primary" type="button" data-dismiss="modal" aria-hidden="true">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- <script src="/js/chatroom.js"></script> -->
<script>
  $(document).ready(function() {

    let limit = 50;
    let flag = limit; // The page already loaded the first 'limit' messages starting at offset = 0 above.

    // Scroll chat box to the end.
    $('#ja-chat-messages').scrollTop($('#ja-chat-messages')[0].scrollHeight);

    // Refresh PHP session so user is not automatically logged out while they have the chat open.
    setInterval(function() {
      $.post('refreshchatsession.php');
    }, 600000);

    // When the user scrolls to the top of the chatbox, load more messages.
    $('#ja-chat-messages').scroll(function() {
      if ($(this).scrollTop() == 0) {
        $('#loader').show();
        $.ajax({
          type: "GET",
          url: "loadmorechatmessages.php",
          data: {
            offset: flag,
            limit: limit
          },
          success: function(data) {
            $('#ja-chat-messages').prepend(data);
            flag += limit;
            $('#loader').hide();
            // Reset scroll if we have NOT reached the last message.
            if (data != '') {
              $('#ja-chat-messages').scrollTop(50);
            }
          }
        });
      }
    });

    let wsdomain = '<?php echo $wsdomain ?>';
    let conn = new WebSocket("ws://" + wsdomain + ":8081");

    conn.onopen = function(e) {
      console.log("Connection established!");
      let username = "<?php echo $username ?>";
      let email = "<?php echo $email ?>";
      let data = {
        'username': username,
        'email': email,
        'text': username + ' has joined the chat',
        'chatstatus': 'joined'
      };
      conn.send(JSON.stringify(data));
    }

    // onmessage received this is what happens:
    conn.onmessage = function(e) {
      let data = JSON.parse(e.data);
      let row = '';
      if (data.chatstatus == 'left') {
        row = `<div class="ja-chat-onemessage-chatstatus">
      <div>${data.text}</div>
      <div>${data.dt}</div>
      </div>
      `;
        $('#' + data.username).removeClass('onlinestatus-background-light').addClass('onlinestatus-background-dark');
        $('#' + data.username + '-dot').removeClass('onlinestatus-dot-green').addClass('onlinestatus-dot-red');
        $('#' + data.username + '-word').removeClass('onlinestatus-word-white').addClass('onlinestatus-word-grey').text('Offline');
      } else if (data.chatstatus == 'joined') {
        row = `<div class="ja-chat-onemessage-chatstatus">
      <div>${data.text}</div>
      <div>${data.dt}</div>
      </div>
      `;
        $('#' + data.username).removeClass('onlinestatus-background-dark').addClass('onlinestatus-background-light');
        $('#' + data.username + '-dot').removeClass('onlinestatus-dot-red').addClass('onlinestatus-dot-green');
        $('#' + data.username + '-word').removeClass('onlinestatus-word-grey').addClass('onlinestatus-word-white').text('Online');
      } else {
        row = `<div class="ja-chat-onemessage">
      <div>${data.gravatar}</div>
      <div>${data.username}<br />${data.text}</div>
      <div>${data.dt}</div>
      </div>
      `;
      }
      // Add the new message row to the chat box.
      $('#ja-chat-messages').append(row);
      // scroll to the bottom.
      $('#ja-chat-messages').scrollTop($('#ja-chat-messages')[0].scrollHeight);
    }

    conn.onclose = function(e) {
      console.log("Connection Closed!");
    }

    // Include an image(s) in a chat message:
    function hideErrorMessages() {
      setTimeout(() => document.querySelector('#chatErrorMessageDiv').style.display = 'none', 5000);
    }

    // Array of images the user chose (original file name + blob url).
    let imageFilenameList = [];
    // Initialize a FormData object to append the images files to that the user chooses.
    let formData = new FormData();

    document.querySelector('#chatImageInput').addEventListener('change', function() {
      for (let i = 0; i < $(this).get(0).files.length; i++) {
        // check file type.
        mimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jfif'];
        if (mimeTypes.indexOf($(this).get(0).files[i].type) == -1) {
          document.querySelector('#chatErrorMessageDiv').style.display = 'block';
          document.querySelector('#chatErrorMessage').innerText = `Error : Only JPEG, PNG, or GIF files allowed.`;
          hideErrorMessages();
          return;
        }
        // check file size (maximum 10 MB)
        const maximumImageSize = 10;
        if ($(this).get(0).files[i].size > maximumImageSize * 1024 * 1024) {
          document.querySelector('#chatErrorMessageDiv').style.display = 'block';
          document.querySelector('#chatErrorMessage').innerText = `Error : Exceeded size ${maximumImageSize}MB.`;
          hideErrorMessages();
          return;
        }
        // check number of files (maximum allowed).
        const maximumNumberOfImages = 10;
        if (imageFilenameList.length >= maximumNumberOfImages) {
          document.querySelector('#chatErrorMessageDiv').style.display = 'block';
          document.querySelector('#chatErrorMessage').innerText = `Error : Maximum ${maximumNumberOfImages} images per chat message.`;
          hideErrorMessages();
          return;
        }
        // Make an object for the image preview's url since browser won't allow path from user's system for the src.
        let imageObjectUrl = URL.createObjectURL($('#chatImageInput').get(0).files[i]);
        imageFilenameList = [...imageFilenameList, {
          imageName: $(this).get(0).files[i].name,
          blobName: imageObjectUrl
        }];
        let previewImage = `<div class="imageThumbnailDiv"><img src=${imageObjectUrl} 
    alt="Preview Thumbnail" class="imageThumbnail">
    <button type="button" class="removeImageThumbnail btn btn-danger btn-block">x</button></div>`;
        $('#previewImages').append(previewImage);
      }
      // Append the chosen image file objects onto the formData object (so we can append to #chatImageInput instead of replacing).
      let files = $("#chatImageInput")[0].files;
      for (var i = 0; i < files.length; i++) {
        formData.append('chatImageInput[]', files[i]);
      }
    });

    // Bind the close (x) clicks to the #previewImages parent. Since the preview images were created
    // dynamically with append, jQuery can only find this appended html using an element (#previewImages)
    // that existed already when the page loaded. Remove from list of files to upload when submitted.
    function findImageToDelete(imageSrcFile) {
      return function(element) {
        return element.blobName === imageSrcFile;
      }
    }
    $('#previewImages').on("click", ".removeImageThumbnail", function() {
      // Removes from imageFilenameList array.
      let imageSrcFile = $(this).parent().children('img').attr('src');
      imageFilenameList.splice(imageFilenameList.findIndex(findImageToDelete(imageSrcFile)), 1);
      // console.log(imageFilenameList);
      // Removes from view.
      $(this).parent().remove();
    });

    // Enlarge the clicked image in a modal.
    $('#ja-chat-messages').on("click", "img", function() {
      let imageSrcFile = $(this).parent().children('img').attr('src');
      $('#modalImage').attr('src', imageSrcFile);
      $('.modal').modal("show");
    });

    // Submitted chat message with enter key or send button.
    $('.ja-chatform').submit(function(e) {
      e.preventDefault();
      let text = $('#msg').val();
      let websocketSend;
      let username = "<?php echo $username ?>";
      let email = "<?php echo $email ?>";
      let attachedImagesHtml = '';
      // Handle any images attached to the message:
      if (imageFilenameList.length > 0) {
        // Start progress bar if there are images.
        $('#progress-bar').show();

        // Attach the list of image file objects to compare with $_FILES in the upload script.
        formData.append('imageFilenameList', JSON.stringify(imageFilenameList));
        // Upload any images to uploads directory:
        $.ajax({
          xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
              if (evt.lengthComputable) {
                var percentComplete = Math.round(evt.loaded / evt.total * 100);
                $('#progress-bar').css({
                  width: percentComplete + '%'
                });
                if (percentComplete === 100) {
                  $('#progress-bar').hide();
                }
              }
            }, true);
            return xhr;
          },
          url: 'uploadimages.php',
          type: 'POST',
          data: formData,
          success: function(data) {
            // Add images to message on successful upload.
            // Data will have an array of image filenames (renamed after their blob names for uniqueness in the upload folder).
            try {
              // Attach new image urls to text to add to the websocketSend object below that we send over websockets.
              attachedImagesHtml += `<div class="chatMessageImage">`;
              let blobFilesArray = JSON.parse(data);
              for (const blobItem of blobFilesArray) {
                // Use the name of the blob image as the image id for uniqueness.
                const blobId = blobItem.split('.')[0];
                attachedImagesHtml += `<div class="oneMessageDiv_normal"><img id="${blobId}" class="image" src="/uploads/${blobItem}"
                alt="Image in chat message"></div>`;
              }
              attachedImagesHtml += `</div>`;
              // Send user's message to the chat, and with any images attached:
              websocketSend = {
                'username': username,
                'email': email,
                'text': text + attachedImagesHtml,
                'chatstatus': 'post'
              };
              conn.send(JSON.stringify(websocketSend));
            } catch (err) {
              console.log(err);
            }
          },
          error: function(err) {
            console.log(err);
          },
          cache: false,
          contentType: false,
          processData: false
        });
      } else {
        // Send user's plain text message to the chat.
        websocketSend = {
          'username': username,
          'email': email,
          'text': text,
          'chatstatus': 'post'
        };
        conn.send(JSON.stringify(websocketSend));
      }
      $('#msg').val(''); // Reset the form field to be empty.
      $('#previewImages').empty(); // Remove images from the preview area.
      imageFilenameList = []; // Remove the files from the array that keeps track of them after the message is sent.
      formData = new FormData(); // Reset formData so a new message will not have the previous message's files attached to it.
    });

    // Update chat login status to 0 by redirecting from /chatroom.
    $('#leave-chat').click(function() {
      let username = "<?php echo $username ?>";
      let email = "<?php echo $email ?>";
      let data = {
        'username': username,
        'email': email,
        'text': username + ' has left the chat',
        'chatstatus': 'left'
      };
      conn.send(JSON.stringify(data));
      window.location.href = '/main';
    });

  });
</script>