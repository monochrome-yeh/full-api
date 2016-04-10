<h3>您的帳號已被鎖定</h3>
<p>原因：帳號遭多次嚐試登入</p>
<p>最後嚐試登入IP: <?= $ip ?></p>
<p>最後嚐試登入資訊:</p>
<ul>
	<li><?= $info['userAgent'] ?></li>
	<li>主機名稱: <?= $info['userHost'] ?></li>
</ul>