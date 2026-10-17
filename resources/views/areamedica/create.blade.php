<!-- Modal Crear Área Médica -->
<div class="modal fade" id="createModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-info">
            <div class="modal-header bg-info text-white">
                <h4 class="modal-title"><i class="fas fa-plus-circle"></i> Nueva Área Médica</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="createForm">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nombre del Área</label>
                            <input type="text" name="Nombre" class="form-control" placeholder="Ej: Fisioterapia" required maxlength="100">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Especialista</label>
                            <input type="text" name="Especialista" class="form-control" placeholder="Ej: Dr. Juan Pérez" required maxlength="100">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Consulta</label>
                        <input type="text" name="TipoConsulta" class="form-control" placeholder="Ej: Lesiones musculares" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="Descripcion" class="form-control" rows="3" placeholder="Detalles adicionales de la atención médica..."></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
