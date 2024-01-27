<div style="background-color:#fff;border:2px solid #ccc;padding:10px;margin-top:20px;">
    <form method="POST" enctype="multipart/form-data" action="#">
        <h1><?php echo __('Add Newspaper', 'sw-newspaper'); ?></h1>
        <table class="form-table" role="presentation">
            <tbody>
                <tr class="user-rich-editing-wrap">
                    <th scope="row"><label for="title">Bezeichnung:</label></th>
                    <td>
                        <input type="text" style="width:300px;" name="title" id="title" class="form-control" value="" required />
                    </td>
                </tr>
                <tr class="user-rich-editing-wrap">
                    <th scope="row"><label for="date">Datum:</label></th>
                    <td>
                        <input type="date" name="date" class="form-control" id="date" required />
                    </td>
                </tr>
                <tr class="user-rich-editing-wrap">
                    <th scope="row"><label for="uploadfile">Neues PDF hochladen:</label></th>
                    <td>
                        <input type="file" name="uploadfile" id="uploadfile" class="form-control" value="" required />
                    </td>
                </tr>
        </table>
        <input type="submit" name="submit" class="button button-primary" value="Hochladen" />
    </form>
</div>