<h1>New website contact message</h1>
<p><strong>Name:</strong> {{ $details['name'] }}</p>
<p><strong>Email:</strong> {{ $details['email'] }}</p>
<p><strong>Phone:</strong> {{ $details['phone'] ?: 'Not provided' }}</p>
<p><strong>Topic:</strong> {{ ucfirst($details['topic']) }}</p>
<h2>Message</h2>
<p>{!! nl2br(e($details['message'])) !!}</p>