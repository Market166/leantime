<form method="POST" action="{{ BASE_URL }}/envvar/capture">
    @csrf
    <div>
        <label>Name</label>
        <input type="text" name="name" pattern="[A-Z][A-Z0-9_]{1,63}" />
    </div>
    <div>
        <label>Value</label>
        <input type="password" name="value" />
    </div>
    <button type="submit">Capture</button>
</form>
