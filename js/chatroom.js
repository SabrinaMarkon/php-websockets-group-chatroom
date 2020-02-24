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
  function hideErrorMessages() {
    setTimeout(() => document.querySelector('#chatErrorMessageDiv').style.display = 'none', 5000);
  }
  let imageFilenameList = []; // Array of image files the user chose.
  document.querySelector('#chatImageInput').addEventListener('change', function() {
    for (let i = 0; i < $(this).get(0).files.length; i++) {
      // check file type.
      mimeTypes = [ 'image/jpeg', 'image/png', 'image/gif' ];
      if(mimeTypes.indexOf($(this).get(0).files[i].type) == -1) {
        document.querySelector('#chatErrorMessageDiv').style.display = 'block';
        document.querySelector('#chatErrorMessage').innerText = `Error : Only JPEG, PNG, or GIF files allowed.`;
        hideErrorMessages();
        return;
      }
      // check file size (maximum 10 MB)
      const maximumImageSize = 10;
      if($(this).get(0).files[i].size > maximumImageSize*1024*1024) {
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
      // imageFilenameList.push($(this).get(0).files[i].name);
      // Make an object for the image preview's url since browser won't allow path from user's system for the src.
      let imageObjectUrl = URL.createObjectURL($('#chatImageInput').get(0).files[i]);
      imageFilenameList.push(imageObjectUrl);
      let previewImage = `<div class="imageThumbnailDiv"><img src=${imageObjectUrl} 
      alt="Preview Thumbnail" class="imageThumbnail">
      <button type="button" class="removeImageThumbnail btn btn-danger btn-block">x</button></div>`;
      $('#previewImages').append(previewImage);
    }
    console.log(imageFilenameList);
  });
  // Bind the close (x) clicks to the #previewImages parent. Since the preview images were created
  // dynamically with append, jQuery can only find this appended html using an element (#previewImages)
  // that existed already when the page loaded. Remove from list of files to upload when submitted.
  $('#previewImages').on("click", ".removeImageThumbnail", function() {
      // Removes from imageFilenameList array.
      let imageSrcFile = $(this).parent().children('img').attr('src');
      let imageIndex = imageFilenameList.indexOf(imageSrcFile);
      imageFilenameList.splice(imageIndex, 1);
      console.log(imageFilenameList);
      console.log(imageSrcFile);
      // Removes from view.
      $(this).parent().remove();
  });
  
  // Submitted chat message with enter key or send button.
  $('.ja-chatform').submit(function(e) {
      e.preventDefault();
      let attachedImagesHtml = '';
      // Handle any images attached to the message:
      if (imageFilenameList.length > 0) {
      // Start progress bar if there are images.
        
      // Upload any images to uploads directory.
  
      // Attach new image urls to text to add to the data object.
  
      }
      let username = "<?php echo $username ?>";
      let email = "<?php echo $email ?>";
      let text = $('#msg').val() + attachedImagesHtml;
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