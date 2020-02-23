$(document).ready(function() {

  let limit = 50;
  let flag = limit; // The page already loaded the first 'limit' messages starting at offset = 0 above.

  // Scroll chat box to the end.
  $('#ja-chat-messages').scrollTop($('#ja-chat-messages')[0].scrollHeight);

  // Refresh PHP session so user is not automatically logged out while they have the chat open.
  setInterval(function() { $.post('refreshchatsession.php'); }, 600000);

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
    conn.send(JSON.stringify(data) );
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
  // When the user selects files, validate the file with the change event.
  document.querySelector('#chatImageInput').addEventListener('change', function() {
    // 'files' array contains the images the user chose.
    let imageObjectUrl = URL.createObjectURL($('#chatImageInput').get(0).files[0]);
    let previewImage = `<div class="imageThumbnailDiv"><img src=${imageObjectUrl} 
    alt="Preview Thumbnail" class="imageThumbnail">
    <button type="button" class="removeImageThumbnail btn btn-danger btn-block">x</button></div>`;
    $('#previewImages').append(previewImage);
  });
  // User clicked the x for an image preview.
  $('.removeImageThumbnail').on('click', function() {
    // $(this).parent('div').remove();
    // $(this).val("");
    console.log(this);
  });

  // Submitted chat message with enter key or send button.
  $('.ja-chatform').submit(function(e) {
      e.preventDefault();
      let username = "<?php echo $username ?>";
      let email = "<?php echo $email ?>";
      let text = $('#msg').val();
      let data = {
        'username': username,
        'email': email,
        'text': text,
        'chatstatus': 'post'
      };
      conn.send(JSON.stringify(data) );
      $('#msg').val(''); // reset the form field to be empty.
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
    conn.send(JSON.stringify(data) );
    window.location.href = '/main';
  });

});
