<!-- Modal Crear -->
<div class="modal fade" id="createModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Entrenamiento</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="createForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Gimnasta</label>
                        <select name="ID_Gimnasta" class="form-control" required>
                            <option value="">Seleccionar Gimnasta</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Aparato</label>
                        <select name="ID_Aparato" class="form-control" required>
                            <option value="">Seleccionar Aparato</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" name="Fecha" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Duración (minutos)</label>
                        <input type="number" name="Duracion" class="form-control" min="1" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="Observaciones" class="form-control" maxlength="50" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
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