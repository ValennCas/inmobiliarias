const API_GEOREF = "https://apis.datos.gob.ar/georef/api";

async function cargarProvincias(selectProvincia, selectCiudad){
    try{
        selectProvincia.innerHTML = `<option value="">Cargando provincias...</option>`;
        selectCiudad.innerHTML = `<option value="">Seleccionar ciudad</option>`;

        selectCiudad.disabled = true;
        const res = await fetch(
            `${API_GEOREF}/provincias?campos=id,nombre&max=100`
        );
        if(!res.ok){
            throw new Error("No se pudieron cargar las provincias");
        }
        const datos = await res.json();
        selectProvincia.innerHTML = `<option value="">Seleccionar provincia</option>`;

        datos.provincias
            .sort(function(a, b){
                return a.nombre.localeCompare(b.nombre);
            })
            .forEach(function(provincia){
                const opcion = document.createElement("option");
                opcion.value = provincia.id;
                opcion.textContent = provincia.nombre;
                selectProvincia.appendChild(opcion);
            });
    }catch(error){
        console.error(error);
        selectProvincia.innerHTML = `<option value="">No se pudieron cargar las provincias</option>`;
    }
}

async function cargarCiudades(idProvincia, selectCiudad){
    if(idProvincia === ""){
        selectCiudad.innerHTML = `<option value="">Seleccionar ciudad</option>`;
        selectCiudad.disabled = true;
        return;
    }

    try{
        selectCiudad.disabled = true;
        selectCiudad.innerHTML = `<option value="">Cargando ciudades...</option>`;
        const res = await fetch(
            `${API_GEOREF}/localidades?provincia=${idProvincia}&campos=id,nombre&max=5000`
        );

        if(!res.ok){
            throw new Error("No se pudieron cargar las ciudades");
        }

        const datos = await res.json();
        selectCiudad.innerHTML = `<option value="">Seleccionar ciudad</option>`;

        datos.localidades
            .sort(function(a, b){
                return a.nombre.localeCompare(b.nombre);
            })
            .forEach(function(ciudad){
                const opcion = document.createElement("option");
                opcion.value = ciudad.nombre;
                opcion.textContent = ciudad.nombre;
                selectCiudad.appendChild(opcion);
            });
        selectCiudad.disabled = false;
    }catch(error){
        console.error(error);
        selectCiudad.innerHTML = `<option value="">No se pudieron cargar las ciudades</option>`;
    }
}