<div id="funnel-content" class="row main"></div>

<!-- MODAL DETAIL SALE -->
<div id="details-sale" role="dialog" tabindex="-1" class="modal fade">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalhes</h4>                              
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
                                <h6 class="text-muted card-subtitle mb-2">Realizada em <sale field="creation_date"></sale> no bairro <sale field="district_name"></sale></h6>                                
                                <div role="alert" class="alert alert-info">
                                    <span><strong>Tempo de atendimento</strong></span><br>
                                    <sale field="average_time"></sale>
                                </div>                                
                                <p class="card-text"><sale field="note"></sale></p>
                                <h5 class="card-title">
                                    <sale field="status"></sale><sale style="margin-left: 28px;" field="status_name"></sale>                                
                                    <validation key="read_full_sale" type="clear">
                                        <validation key="update_sale" type="clear">
                                            <i class="icon-note status_edit" name="owner_edit" onclick="showStatusEdit();"></i>
                                            <select onchange="updateStatusSale($(this).val(), localStorage.getItem('seller_id'), $('#status_name option:selected').html(), true);" name="status_name" class="status_name" id="status_name"> 
                                                <?php
                                                foreach ($status as $item):
                                                    if ((int) $item['id'] != 0):
                                                        ?>
                                                        <option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                                                        <?php
                                                    endif;
                                                endforeach;
                                                ?>
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
                                    <i class="icon-pin mr-2"></i><sale field="owner_name"></sale>&nbsp;<span style="color: #6c757d">(setor)</span>                                    
                                    <validation key="read_full_sale" type="clear">
                                        <validation key="update_sale" type="clear">
                                            <i class="icon-note owner_edit" name="owner_edit" onclick="showOwnerEdit();"></i>
                                            <select onchange="if ($(this).val() !== '0') {
                                                        updateSectorSale($(this).val(), localStorage.getItem('seller_id'))
                                                    }" name="sector_name" class="owner_name" id="sector_name">                                        
                                                <option value="0">Selecione</option>
                                                <?php foreach ($sectors as $item): ?>
                                                    <option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </validation>
                                    </validation>
                                </h5>                                
                                <h5 class="card-title">
                                    <i class="icon-handbag mr-2"></i><sale field="seller_name"></sale>&nbsp;<span style="color: #6c757d">(vendedor)</span>
                                </h5>
                                <h5 class="card-title">
                                    <i class="icon-home mr-2"></i><sale class="mr-2" field="client_name"></sale><sale field="client_is_holder"></sale>
                                </h5>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted card-subtitle mb-2">
                                            <i class="icon-envelope mr-2"></i><sale field="client_email"></sale>
                                        </h6>
                                        <h6 class="text-muted card-subtitle mb-2">
                                            <i class="icon-phone mr-2"></i><sale field="client_telephone"></sale>
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
                                <sale field="audio"></sale>
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

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXl0mdLvNt_OiME0GzlZbZrTrzAlPlrME"></script>