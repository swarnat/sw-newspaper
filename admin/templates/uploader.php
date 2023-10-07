<div style="background-color:#fff;border:2px solid #ccc;padding:10px;margin-top:20px;">
    <form method="POST" enctype="multipart/form-data" action="#">
        <label>Bezeichnung: <input type="text" name="title" value="" required /></label>
        <br/>
        <br/>
        <label>Datum: <input type="date" name="date" required /></label>
        <br/>
        <br/>
        <label>Neues PDF hochladen: <input type="file" name="uploadfile" value="" required /></label>
        <br/>
        <br/>
        <input type="submit" name="submit" class="button button-primary" value="Hochladen" />
    </form>
</div>