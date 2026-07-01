import { useState, useRef, useCallback, useEffect } from 'react';

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

export default function useSpeechToText() {
    const [isSupported] = useState(() => !!SpeechRecognition);
    const [isListening, setIsListening] = useState(false);
    const [isMicDenied, setIsMicDenied] = useState(false);
    const [error, setError] = useState('');
    const recognitionRef = useRef(null);

    useEffect(() => {
        return () => {
            if (recognitionRef.current) {
                recognitionRef.current.stop();
                recognitionRef.current = null;
            }
        };
    }, []);

    const startListening = useCallback(({ onResult, onInterim }) => {
        if (!SpeechRecognition) {
            setError('Browser tidak mendukung Web Speech API.');
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = 'id-ID';

        recognition.onresult = (event) => {
            let final = '';
            let interim = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    final += transcript;
                } else {
                    interim += transcript;
                }
            }
            if (final) onResult?.(final);
            if (interim) onInterim?.(interim);
        };

        recognition.onerror = (event) => {
            if (event.error === 'not-allowed') {
                setIsMicDenied(true);
                setError('Izin mikrofon ditolak. Izinkan akses mikrofon di pengaturan browser.');
            } else if (event.error === 'no-speech') {
                setError('Tidak ada suara yang terdeteksi. Coba lagi.');
            } else {
                setError(`Speech recognition error: ${event.error}`);
            }
            setIsListening(false);
        };

        recognition.onend = () => {
            setIsListening(false);
        };

        try {
            recognition.start();
            recognitionRef.current = recognition;
            setIsListening(true);
            setIsMicDenied(false);
            setError('');
        } catch {
            setError('Gagal memulai speech recognition.');
        }
    }, []);

    const stopListening = useCallback(() => {
        if (recognitionRef.current) {
            recognitionRef.current.stop();
            recognitionRef.current = null;
        }
        setIsListening(false);
    }, []);

    return {
        isSupported,
        isListening,
        isMicDenied,
        error,
        setError,
        startListening,
        stopListening,
    };
}
