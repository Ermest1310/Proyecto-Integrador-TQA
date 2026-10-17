<!-- Modal Editar Área Médica -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-white">
                <h4 class="modal-title"><i class="fas fa-edit"></i> Editar Área Médica</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nombre del Área</label>
                            <input type="text" name="Nombre" id="editNombre" class="form-control" required maxlength="100">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Especialista</label>
                            <input type="text" name="Especialista" id="editEspecialista" class="form-control" required maxlength="100">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Consulta</label>
                        <input type="text" name="TipoConsulta" id="editTipoConsulta" class="form-control" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="Descripcion" id="editDescripcion" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>


