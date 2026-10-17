<!-- Modal Editar -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Lesión</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editId">
                    <div class="form-group">
                        <label>Gimnasta</label>
                        <select name="ID_Gimnasta" id="editGimnasta" class="form-control" required>
                            <option value="">Seleccionar Gimnasta</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Lesión</label>
                        <input type="text" name="Tipo" id="editTipo" class="form-control" maxlength="30" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Parte del Cuerpo</label>
                        <select name="ParteCuerpo" id="editParteCuerpo" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="Cabeza">Cabeza</option>
                            <option value="Cuello">Cuello</option>
                            <option value="Hombro">Hombro</option>
                            <option value="Brazo">Brazo</option>
                            <option value="Codo">Codo</option>
                            <option value="Antebrazo">Antebrazo</option>
                            <option value="Muñeca">Muñeca</option>
                            <option value="Mano">Mano</option>
                            <option value="Espalda">Espalda</option>
                            <option value="Pecho">Pecho</option>
                            <option value="Abdomen">Abdomen</option>
                            <option value="Cadera">Cadera</option>
                            <option value="Muslo">Muslo</option>
                            <option value="Rodilla">Rodilla</option>
                            <option value="Pierna">Pierna</option>
                            <option value="Tobillo">Tobillo</option>
                            <option value="Pie">Pie</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Parte Específica</label>
                        <input type="text" name="ParteEspecifica" id="editParteEspecifica" class="form-control" maxlength="50">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Lesión</label>
                        <input type="date" name="FechaLesion" id="editFechaLesion" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Gravedad</label>
                        <select name="Gravedad" id="editGravedad" class="form-control" required>
                            <option value="">Seleccionar Gravedad</option>
                            <option value="Leve">Leve</option>
                            <option value="Moderada">Moderada</option>
                            <option value="Grave">Grave</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="Estado" id="editEstado" class="form-control" required>
                            <option value="">Seleccionar Estado</option>
                            <option value="Lesionado">Lesionado</option>
                            <option value="En Rehabilitación">En Rehabilitación</option>
                            <option value="Recuperada">Recuperada</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Recuperación (opcional)</label>
                        <input type="date" name="FechaRecuperacion" id="editFechaRecuperacion" class="form-control">
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">Solo completar si el estado es "Recuperada"</small>
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