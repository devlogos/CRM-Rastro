<div class="row">
    <div class="col">
        <div class="card mt-2">
            <div class="card-header">
                <form class="form-inline">
                    <div class="form-group mt-2 mr-2">
                        <label for="dateinitial" class="mr-2">De</label>
                        <input type="text" readonly="true" name="dateinitial" data-timepicker="true" data-language="pt-br" data-multiple-dates="3" data-multiple-dates-separator=", " data-position="bottom left" autocomplete="off" class="form-control date" maxlength="16" id="dateinitial" />
                    </div>
                    <div class="form-group mt-2 mr-2">
                        <label for="datefinal" class="mr-2">até</label>
                        <input type="text" readonly="true" name="datefinal" data-timepicker="true" data-language="pt-br" data-multiple-dates="3" data-multiple-dates-separator=", " data-position="bottom left" autocomplete="off" class="form-control date" maxlength="16" id="datefinal" />
                    </div>
                    <div class="form-group mt-2 mr-2">
                        <select name="seller_id" class="form-control select2" id="seller_id">
                            <option value="0">Todos</option>
                            <?php
                            foreach ($sellers as $item):
                                if ((int) $item['id'] != 0):
                                    ?>
                                    <option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                                    <?php
                                endif;
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <button class="btn btn-info filter-trackback" type="button"><i class="icon-magnifier"></i></button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div role="group" class="btn-group">
                    <button class="btn btn-primary" type="button">Button 1</button>
                    <button class="btn btn-primary" type="button">Button 1</button>
                    <button class="btn btn-primary" type="button">Button 2</button>
                </div>
                <div id="mapTrackback" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
<script async defer src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAXl0mdLvNt_OiME0GzlZbZrTrzAlPlrME" ></script>