<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Video Downloader</title>
<style>
  /* Same styling as before */
  body {
    font-family: Arial, sans-serif;
    text-align: center;
    margin-top: 50px;
  }
  input, select, button {
    padding: 10px;
    font-size: 16px;
    margin: 10px;
  }
  #result {
    margin-top: 30px;
    padding: 15px;
    border: 1px solid #ddd;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    word-wrap: break-word;
    background-color: #f9f9f9;
  }
  .media-item {
    margin-bottom: 10px;
  }
  .media-item strong {
    text-transform: uppercase;
  }
  .download-btn {
    margin-left: 10px;
    padding: 5px 10px;
    font-size: 14px;
    text-decoration: none;
    background-color: #28a745;
    color: white;
    border-radius: 4px;
  }
  .download-btn:hover {
    background-color: #218838;
  }
</style>
</head>
<body>
  <h1>Video Downloader</h1>
  <form id="downloadForm">
    <input type="url" id="urlInput" placeholder="Enter video URL" required />
    <select id="platformSelect" required>
      <option value="" disabled selected>Select platform</option>
      <option value="youtube">YouTube</option>
      <option value="tiktok">TikTok</option>
      <option value="instagram">Instagram</option>
      <option value="facebook">Facebook</option>
      <option value="twitter">Twitter</option>
    </select>
    <button type="submit">Download</button>
  </form>

  <div id="result"></div>

  <script>
    document.getElementById('downloadForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      const url = document.getElementById('urlInput').value.trim();
      const platform = document.getElementById('platformSelect').value.trim();

      const resultDiv = document.getElementById('result');
      resultDiv.innerHTML = 'Loading...';

      if (!url || !platform) {
        resultDiv.innerHTML = 'Please provide both URL and platform.';
        return;
      }

      try {
        const apiUrl = 'https://fsmvid.com/api/proxy';

        const postData = {
          url: url,
          platform: platform.toLowerCase()
        };

        const response = await fetch(apiUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
            // User-Agent, Origin, Referer, sec-ch-ua etc cannot be set here by browser JS
          },
          body: JSON.stringify(postData),
          mode: 'cors' // allow cross-origin if server permits
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data && data.medias && Array.isArray(data.medias) && data.medias.length > 0) {
          resultDiv.innerHTML = '<h3>Select Quality:</h3>';
          data.medias.forEach(media => {
            const quality = (media.quality || 'Unknown').toUpperCase();
            const mediaUrl = media.url || '#';

            const item = document.createElement('div');
            item.className = 'media-item';
            item.innerHTML = `
              <strong>${quality}</strong>
              <a href="${mediaUrl}" target="_blank" rel="noopener noreferrer" class="download-btn">Download</a>
            `;
            resultDiv.appendChild(item);
          });
        } else if(data.error) {
          resultDiv.innerHTML = `<span style="color:red;">Error: ${data.error}</span>`;
        } else {
          resultDiv.innerHTML = 'No downloadable media found.';
        }

      } catch (err) {
        console.error(err);
        resultDiv.innerHTML = `<span style="color:red;">Error: ${err.message}</span>`;
      }
    });
  </script>
</body>
</html>
