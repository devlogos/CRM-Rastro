<div class="row main">
    <div class="table-responsive">
        <table id="sellers" class="display table table-striped table-bordered" width="100%">
            <thead>
                <tr>
                    <th>
                        <div onchange="checkAllSellers()" class="custom-control custom-control-seller custom-checkbox mb-3 mt-2">
                            <input type="checkbox" class="custom-control-input" id="check_all">
                            <label class="custom-control-label" for="check_all">&nbsp;</label>
                        </div>
                    </th>
                    <th>Vendedor</th>
                    <th>Perfil</th>
                    <th>Vendas</th>
                    <th>Efetivadas</th>
                    <th>Meta</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Ação</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- MODAL NOTIFICATION -->
<div id="notification" role="dialog" class="modal fade">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notificação</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="card mt-2">
                    <div class="card-body">
                        <form class="form-notification" action="../sellers/notification" onsubmit="return valSendNotification()" method="post">
                            <input name="companyid" class="companyid" type="hidden" />
                            <div class="form-group token-group">
                            </div>
                            <div class="form-group">
                                <label for="title"><span class="mr-1 required">*</span>Título</label>
                                <input type="text" name="title" autocomplete="off" class="form-control" id="title" maxlength="50" />
                            </div>
                            <div class="form-group badges-group">
                            </div>
                            <div class="form-group">
                                <label for="message"><span class="mr-1 required">*</span>Mensagem</label>
                                <textarea name="message" class="form-control" id="message"></textarea>
                            </div>
                            <div class="form-group mt-3">
                                <button class="btn btn-info button-new-sale" type="submit">Notificar</button>
                            </div>
                            <div class="result-notification"></div>
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
<!-- MODAL NOTIFICATION -->

<!-- MODAL NEW SELLER -->
<div id="new-seller" role="dialog" class="modal fade">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Novo Vendedor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="card mt-2">
                    <div class="card-body">
                        <form id="form-new-seller" class="form-new-seller" action="../sellers/create" onsubmit="return valNewSeller()" method="post">
                            <input name="companyid" class="companyid" type="hidden" />
                            <div class="d-block">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a class="nav-link tab-basicdata active" role="tab" data-toggle="tab" href="#tab-basicdata"><i class="icon-handbag icon-seller"></i></a></li>
                                    <li class="nav-item"><a class="nav-link tab-settingsdata" role="tab" data-toggle="tab" href="#tab-settingsdata"><i class="icon-settings icon-seller"></i></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active mt-2" role="tabpanel" id="tab-basicdata">
                                        <div class="form-group">
                                            <label for="name"><span class="mr-1 required">*</span>Nome</label>
                                            <seller field="name"></seller>
                                            <input type="text" name="name" autocomplete="off" class="form-control" id="name" maxlength="150" />
                                        </div>
                                        <div class="form-group">
                                            <input type="file" name="url_image" id="url_image" />
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
                                            <label for="user"><span class="mr-1 required">*</span>Usuário</label>
                                            <input type="text" name="user" autocomplete="off" class="form-control" id="user" maxlength="15" />
                                        </div>
                                        <div class="form-group">
                                            <label for="password"><span class="mr-1 required">*</span>Senha</label>
                                            <input type="password" name="password" autocomplete="off" class="form-control" id="password" maxlength="15" />
                                            <span class="form-important">Por segurança, utilize caracteres como @, &, ?, ! e .</span>
                                        </div>
                                    </div>
                                    <div class="tab-pane mt-2" role="tabpanel" id="tab-settingsdata">
                                        <fieldset>
                                            <legend>Bairros</legend>
                                            <div class="form-group">
                                                <label for="state_id" class="label-block"><span class="mr-1 required">*</span>Estado</label>
                                                <select onchange="readListCities($(this).val(), '.cities-content', false, true);" name="state_id[]" class="form-control select2" id="state_id" multiple="multiple">
                                                    <option value="0">Selecione</option>
                                                    <?php foreach ($states as $item) : ?>
                                                        <option value="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group cities-content"></div>
                                        </fieldset>
                                        <fieldset>
                                            <legend>Gravação</legend>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="recording_time"><span class="mr-1 required">*</span>Tempo (segundos)</label>
                                                        <input type="number" value="600" name="recording_time" autocomplete="off" class="form-control" id="recording_time" />
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="sample_rate"><span class="mr-1 required">*</span>Taxa de amostragem</label>
                                                        <input type="number" value="40" name="sample_rate" autocomplete="off" class="form-control" id="sample_rate" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="bits_per_sample"><span class="mr-1 required">*</span>Bits p/ amostra</label>
                                                        <input type="number" value="32" name="bits_per_sample" autocomplete="off" class="form-control" id="bits_per_sample" />
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="send_after_sale">Após venda</label>
                                                        <div class="custom-control custom-checkbox mb-3 mt-2">
                                                            <input disabled type="checkbox" name="send_after_sale" class="custom-control-input" id="send_after_sale">
                                                            <label class="custom-control-label" for="send_after_sale">Sincronizar</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <button class="btn btn-info button-new-sale" type="submit">Adicionar</button>
                            </div>
                            <div class="result-new-seller"></div>
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
<!-- MODAL NEW SELLER -->