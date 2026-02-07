{{-- 
    Typewriter - Efecto de texto escribiéndose
    Usa Alpine.js para la animación cliente-side
--}}
<span 
    x-data="{
        texts: {{ json_encode($texts) }},
        currentIndex: 0,
        displayText: '',
        charIndex: 0,
        typing: true,
        loop: {{ $loop ? 'true' : 'false' }},
        speed: {{ $typingSpeed }},
        pauseDuration: {{ $pauseDuration }},
        
        init() {
            this.typeText();
        },
        
        typeText() {
            if (this.texts.length === 0) return;
            
            const currentText = this.texts[this.currentIndex];
            
            if (this.typing) {
                if (this.charIndex < currentText.length) {
                    this.displayText += currentText.charAt(this.charIndex);
                    this.charIndex++;
                    setTimeout(() => this.typeText(), this.speed);
                } else {
                    // Terminó de escribir, pausar antes de borrar
                    setTimeout(() => {
                        this.typing = false;
                        this.deleteText();
                    }, this.pauseDuration);
                }
            }
        },
        
        deleteText() {
            if (this.displayText.length > 0) {
                this.displayText = this.displayText.slice(0, -1);
                setTimeout(() => this.deleteText(), this.speed / 2);
            } else {
                // Terminó de borrar, pasar al siguiente texto
                this.charIndex = 0;
                this.currentIndex = (this.currentIndex + 1) % this.texts.length;
                
                if (this.loop || this.currentIndex !== 0) {
                    this.typing = true;
                    setTimeout(() => this.typeText(), this.speed);
                }
            }
        }
    }"
    class="inline"
>
    <span x-text="displayText"></span>
    <span class="typewriter-cursor animate-pulse">|</span>
</span>
