<!-- Modal Crear -->
<div class="modal fade" id="createModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Aparato</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del Aparato *</label>
                        <input type="text" name="Nombre" class="form-control" maxlength="20" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Estado de Mantenimiento</label>
                        <select name="EstadoMantenimiento" class="form-control">
                            <option value="Operativo" selected>Operativo</option>
                            <option value="Mantenimiento">En Mantenimiento</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Último Mantenimiento</label>
                                <input type="date" name="UltimoMantenimiento" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Próximo Mantenimiento</label>
                                <input type="date" name="ProximoMantenimiento" class="form-control" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>