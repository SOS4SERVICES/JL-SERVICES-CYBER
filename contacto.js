document.addEventListener("DOMContentLoaded", () => {
  
  const formulario = document.getElementById("contact-form");
  
  if (!formulario) return;
  
  formulario.addEventListener("submit", async function(e) {
    
    e.preventDefault();
    
    const boton = formulario.querySelector('button[type="submit"]');
    
    const nombre = formulario.nombre.value.trim();
    const telefono = formulario.telefono.value.trim();
    const email = formulario.email.value.trim();
    const mensaje = formulario.mensaje.value.trim();
    
    if (!nombre || !email || !mensaje) {
      alert("Por favor completa los campos obligatorios.");
      return;
    }
    
    const textoOriginal = boton.innerHTML;
    
    boton.disabled = true;
    boton.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    
    const datos = new FormData(formulario);
    
    try {
      
      const respuesta = await fetch("enviar-correo.php", {
        method: "POST",
        body: datos
      });
      
      const texto = await respuesta.text();
      
      let resultado;
      
      try {
        
        resultado = JSON.parse(texto);
        
      } catch {
        
        console.error("Respuesta PHP:", texto);
        
        throw new Error(
          "El servidor no devolvió una respuesta válida."
        );
        
      }
      
      if (resultado.success) {
        
        alert(
          "Mensaje enviado correctamente. Gracias por contactar a SOS4 SERVICES."
        );
        
        formulario.reset();
        
      } else {
        
        alert(
          resultado.message ||
          "No fue posible enviar el mensaje."
        );
        
      }
      
    } catch (error) {
      
      console.error("Error:", error);
      
      alert(
        "Ocurrió un problema al enviar el mensaje. Intenta nuevamente."
      );
      
    } finally {
      
      boton.disabled = false;
      boton.innerHTML = textoOriginal;
      
    }
    
  });
  
});