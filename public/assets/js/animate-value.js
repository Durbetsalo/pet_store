function animateValue(element, start, end, duration = 400, formatter = null) {
    if(element.animationId){
        cancelAnimationFrame(element.animationId)
    }
    const startValue = typeof start === 'string' ? parseInt(start.replace(/[^\d-]/g, ''), 10) : start;
    const endValue = typeof end === 'string' ? parseInt(end.replace(/[^\d-]/g, ''), 10) : end;
    let startTime = null;

    const animate = (currentTime) =>{
        
        if(!startTime){
            startTime = currentTime
        }

        const progress = Math.min((currentTime - startTime) / duration, 1)
        
        const currentValue = Math.floor(progress * (endValue - startValue) + startValue)

        element.textContent = formatter ? formatter(currentValue) : currentValue
        if(progress != 1){
            requestAnimationFrame(animate)
        }else{
            element.textContent = formatter ? formatter(endValue) : endValue
        }
    }

    element.animationId = requestAnimationFrame(animate)
}