<div class="nyroModal modal">
    <h3>Capture environment variable</h3>
    <form method="POST" action="{{ BASE_URL }}/envvar/capture">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required pattern="[A-Z][A-Z0-9_]{1,63}" placeholder="MY_VAR_NAME" />
        </div>
        <div class="form-group">
            <label for="value">Value</label>
            <input type="password" name="value" id="value" required />
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="javascript:void(0);" class="btn btn-default nyroModalClose">Cancel</a>
    </form>
</div>