
<html>
    <?php
        Flight::render("components/head");
    ?>

    <body>
        <?php
            Flight::render("components/form_alerts");
        ?>
        <h1>
            Upload
        </h1>

        <form method="post" enctype="multipart/form-data">
            <p>
                <input type="text" name="name" title="Track Name">
                <input type="text" name="description" title="Track Description">
                <select name="privacy" title="Track Privacy">
                    <option value="public" selected >Public </option>
                    <option value="private" selected >Private </option>
                    <option value="personal" selected >Personal </option>
                </select>
            </p>
            <p>
                <input type="file" name="track" accept=".mp3,.wav,.flac">
            </p>
            <p>
                <input type="submit">
            </p>
        </form>


    <?php
        Flight::render("components/footer");
    ?>
</html>
