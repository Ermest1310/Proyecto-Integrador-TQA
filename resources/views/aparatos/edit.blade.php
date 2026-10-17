<!-- Modal Editar -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Aparato</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="form-group">
                        <label>Nombre del Aparato *</label>
                        <input type="text" name="Nombre" id="editNombre" class="form-control" maxlength="20" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Estado de Mantenimiento</label>
                        <select name="EstadoMantenimiento" id="editEstadoMantenimiento" class="form-control">
                            <option value="Operativo">Operativo</option>
                            <option value="Mantenimiento">En Mantenimiento</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Último Mantenimiento</label>
                                <input type="date" name="UltimoMantenimiento" id="editUltimoMantenimiento" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Próximo Mantenimiento</label>
                                <input type="date" name="ProximoMantenimiento" id="editProximoMantenimiento" class="form-control" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>