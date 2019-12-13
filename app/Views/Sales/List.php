<?php
// get finished status id

storage('finished_id', getValueArray($status, 'finished', 1, 'id'));

// get finished status name

storage('finished_name', getValueArray($status, 'finished', 1, 'name'));

// get finished status color

storage('finished_color', getValueArray($status, 'finished', 1, 'color'));
?>
<div class="row">
    <div class="col">
        <div class="card mt-2">
            <div class="card-header">
                <form class="form-inline">
                    <div class="form-group mt-2 mr-2">
                        <label for="dateinitial" class="mr-2">De</label>
                        <input type="text" readonly="true" name="dateinitial" data-timepicker="false" data-language="pt-br" data-multiple-dates="1" data-multiple-dates-separator=", " data-position="bottom left" autocomplete="off" class="form-control date" maxlength="16" id="dateinitial" />
                    </div>
                    <div class="form-group mt-2 mr-2">
                        <label for="datefinal" class="mr-2">até</label>
                        <input type="text" readonly="true" name="datefinal" data-timepicker="false" data-language="pt-br" data-multiple-dates="1" data-multiple-dates-separator=", " data-position="bottom left" autocomplete="off" class="form-control date" maxlength="16" id="datefinal" />
                    </div>
                    <div class="form-group mt-2 mr-2">
                        <select name="search_status_id" class="form-control select2" id="search_status_id" multiple="multiple">
                            <option value="0">Status</option>
                            <?php foreach ($status as $item) :
                                if ((int) $item['id'] != 0) : ?>
                                    <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                            <?php endif;
                            endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mt-2 mr-2">
                        <select name="search_seller_id" class="form-control select2" id="search_seller_id">
                            <option value="0">Vendedor</option>
                            <?php foreach ($sellers as $item) :
                                if ((int) $item['id'] != 0) : ?>
                                    <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                            <?php endif;
                            endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <button class="btn btn-info filter-sales" type="button"><i class="icon-magnifier"></i></button>
                    </div>
                    <div class="form-group ml-2 mt-2">
                        <button class="btn btn-info reset-sales" type="button"><i class="icon-refresh"></i></button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sales" class="display table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Data</th>
                                <th>Vendedor</th>
                                <th>Produto</th>
                                <th>Bairro</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL SALE -->
<div id="details-sale" role="dialog" tabindex="-1" class="modal fade">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalhes</h4>
                <div>
                    <validation key="read_sample_sale" type="clear">
                        <validation key="update_sale" type="clear">
                            <sale field="button_finally"></sale>
                        </validation>
                    </validation>
                    <validation key="read_full_sale">
                        <button class="btn btn-info next-sale ml-1" type="button">Avançar</button>
                    </validation>
                </div>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a role="tab" data-toggle="tab" href="#details" class="nav-link active">
                            <i class="icon-event icon-tab-detail"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a role="tab" data-toggle="tab" href="#contacts" class="nav-link">
                            <i class="icon-notebook icon-tab-detail"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a role="tab" data-toggle="tab" href="#geolocation" class="nav-link">
                            <i class="icon-location-pin icon-tab-detail"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a role="tab" data-toggle="tab" href="#audio" class="nav-link">
                            <i class="icon-playlist icon-tab-detail"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a role="tab" data-toggle="tab" href="#images" class="nav-link">
                            <i class="icon-picture icon-tab-detail"></i>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="details">
                        <div class="card mt-2">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <sale field="code"></sale>
                                </h4>
                                <h5 class="text-muted card-subtitle mb-2">
                                    <sale field="category_name"></sale>&nbsp;(categoria)&nbsp;-&nbsp;<sale field="product_name"></sale>
                                </h5>
                                <h6 class="text-muted card-subtitle mb-2">Realizada em <sale field="creation_date"></sale> no bairro <sale field="district_name"></sale>
                                </h6>
                                <div role="alert" class="alert alert-info">
                                    <span><strong>Tempo de atendimento</strong></span><br>
                                    <sale field="average_time"></sale>
                                </div>
                                <p class="card-text">
                                    <sale field="note"></sale>
                                </p>
                                <h5 class="card-title">
                                    <sale field="status"></sale>
                                    <sale style="margin-left: 28px;" field="status_name"></sale>
                                    <validation key="read_full_sale" type="clear">
                                        <validation key="update_sale" type="clear">
                                            <i class="icon-note status_edit" name="owner_edit" onclick="showStatusEdit();"></i>
                                            <select onchange="updateStatusSale($(this).val(),localStorage.getItem('seller_id'), $('#status_name option:selected').html());" name="status_name" class="status_name" id="status_name">
                                                <?php foreach ($status
                                                    as $item) :
                                                    if (
                                                        (int) $item['id'] != 0
                                                    ) : ?>
                                                        <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                                <?php endif;
                                                endforeach; ?>
                                            </select>
                                        </validation>
                                    </validation>
                                </h5>
                                <h5>
                                    <sale field="reason"></sale>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="contacts">
                        <div class="card mt-2">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="icon-pin mr-2"></i>
                                    <sale field="owner_name"></sale>&nbsp;<span style="color: #6c757d">(setor)</span>
                                    <validation key="read_full_sale" type="clear">
                                        <validation key="update_sale" type="clear">
                                            <i class="icon-note owner_edit" name="owner_edit" onclick="showOwnerEdit();"></i>
                                            <select onchange="if ($(this).val() !== '0'){updateSectorSale($(this).val(),localStorage.getItem('seller_id'))}" name="sector_name" class="owner_name" id="sector_name">
                                                <option value="0">Selecione</option>
                                                <?php foreach ($sectors
                                                    as $item) : ?>
                                                    <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </validation>
                                    </validation>
                                </h5>
                                <h5 class="card-title">
                                    <i class="icon-handbag mr-2"></i>
                                    <sale field="seller_name"></sale>&nbsp;<span style="color: #6c757d">(vendedor)</span>
                                </h5>
                                <h5 class="card-title">
                                    <i class="icon-home mr-2"></i>
                                    <sale class="mr-2" field="client_name"></sale>
                                    <sale field="client_is_holder"></sale>
                                </h5>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted card-subtitle mb-2">
                                            <i class="icon-envelope mr-2"></i>
                                            <sale field="client_email"></sale>
                                        </h6>
                                        <h6 class="text-muted card-subtitle mb-2">
                                            <i class="icon-phone mr-2"></i>
                                            <sale field="client_telephone"></sale>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="geolocation">
                        <div class="card mt-2">
                            <div class="card-body">
                                <sale field="map"></sale>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="audio">
                        <div class="card mt-2">
                            <div class="card-body">
                                <sale field="audio" style="display:flex;flex-direction: row;justify-content: space-between;align-items: center;"></sale>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="images">
                        <div class="card mt-2">
                            <div class="card-body">
                                <sale field="documents"></sale>
                                <div id="result-convert" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <validation key="read_full_sale" type="clear">
                    <button class="btn btn-dark" type="button" data-dismiss="modal">Fechar</button>
                </validation>
            </div>
        </div>
    </div>
</div>
<!-- MODAL DETAIL SALE -->

<!-- MODAL NEW CLIENT -->
<div id="new-client" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-over" role="document">
        <div class="modal-content modal-content-over">
            <div class="modal-header">
                <h4 class="modal-title">Novo Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="card mt-2">
                    <div class="card-body">
                        <form id="form-add-client" class="form-add-client" onsubmit="return valNewClient()" method="post">
                            <input name="companyid" class="companyid" type="hidden" />
                            <div class="form-group">
                                <label for="name"><span class="mr-1 required">*</span>Nome</label>
                                <seller field="name"></seller>
                                <input type="text" name="name" autocomplete="off" class="form-control" id="name" maxlength="150" />
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" name="email" autocomplete="off" class="form-control" id="email" maxlength="150" />
                            </div>
                            <div class="form-group">
                                <label for="telephone">Telefone</label>
                                <input type="tel" name="telephone" autocomplete="off" class="form-control" id="telephone" maxlength="12" />
                            </div>
                            <div class="form-group">
                                <button class="btn btn-info button-new-sale" type="submit">Adicionar</button>
                            </div>
                            <div class="result-new-client"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" type="button" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL NEW CLIENT -->

<!-- MODAL NEW SALE -->
<div id="new-sale" role="dialog" class="modal fade">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nova Venda (Agendamento)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="card mt-2">
                    <div class="card-body">
                        <form id="form-scheduled-sale" class="form-scheduled-sale" onsubmit="return valNewSale()" method="post">
                            <input name="send_audio_file" class="send_audio_file" type="hidden" value="0" />
                            <div class="form-group">
                                <label for="creation_date"><span class="mr-1 required">*</span>Data de agendamento</label>
                                <input type="text" readonly="true" name="creation_date" data-timepicker="true" data-language="pt-br" data-position="bottom left" autocomplete="off" class="form-control date" maxlength="16" id="creation_date" />
                            </div>
                            <div class="form-group">
                                <label for="code"><span class="mr-1 required">*</span>Código</label>
                                <input type="text" name="code" autocomplete="off" class="form-control" id="code" maxlength="15" />
                                <span class="form-important">Unidade ou referência do produto</span>
                            </div>
                            <div class="form-group">
                                <label for="seller_id"><span class="mr-1 required">*</span>Vendedor</label>
                                <select name="seller_id" class="form-control select2" id="seller_id">
                                    <option value="0">Selecione</option>
                                    <?php foreach ($sellers as $item) : ?>
                                        <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <fieldset>
                                <legend>Produto</legend>
                                <div class="form-group">
                                    <label for="category_id" class="label-block"><span class="mr-1 required">*</span>Categoria</label>
                                    <select onchange="if ($(this).val() !== '0'){readListProducts($(this).val(), '.products-content');}" name="category_id" class="form-control select2" id="category_id">
                                        <option value="-1">Selecione</option>
                                        <?php foreach ($categories as $item) : ?>
                                            <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group products-content"></div>
                            </fieldset>
                            <fieldset>
                                <legend>Bairro</legend>
                                <div class="form-group">
                                    <label for="state_id" class="label-block"><span class="mr-1 required">*</span>Estado</label>
                                    <select onchange="readListCities($(this).val(), '.cities-content');" name="state_id" class="form-control select2" id="state_id">
                                        <option value="-1">Selecione</option>
                                        <?php foreach ($states as $item) : ?>
                                            <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group cities-content"></div>
                                <div class="form-group districts-content"></div>
                            </fieldset>
                            <div class="form-group d-flex flex-row-reverse justify-content-between">
                                <i class="icon-plus new-client"></i>
                                <div class="clients-content"></div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox mb-3 mt-2">
                                    <input type="checkbox" name="client_is_holder" class="custom-control-input" id="client_is_holder">
                                    <label class="custom-control-label" for="client_is_holder">Titular</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="note">Observação</label>
                                <textarea name="note" placeholder="Digite aqui sua observação..." class="form-control" rows="2" id="note"></textarea>
                            </div>
                            <input type="hidden" name="shipping_id" value="4" />
                            <div class="form-group">
                                <button class="btn btn-info button-new-sale" type="submit">Agendar</button>
                            </div>
                            <div class="result-scheduled-sale"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" type="button" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL NEW SALE -->

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXl0mdLvNt_OiME0GzlZbZrTrzAlPlrME"></script>