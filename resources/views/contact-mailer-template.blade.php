<div>
    <div>
        From: <a href="mailto:{{ $contact->email }}"> {{ $contact->first_name }} {{ $contact->last_name }}
            <{{ $contact->email }}>
        </a>
    </div>
    <p>
        {{ $contact->message }}
    </p>
    <div>
        Received: {{ $contact->created_at }}
    </div>
</div>
