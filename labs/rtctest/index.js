async function playVideoFromCamera() {
    try {
        const constraints = {'video': true, 'audio': true};
        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        const videoElement = document.querySelector('video#localVideo');
        videoElement.srcObject = stream;
    } catch (error) {
        console.error ('Error opening video camera.', error);
    }
}

async function makeCall() {
    const configuration = {'iceServers': [{'urls': 'stun:stun.1.google.com:19302'}]};
    const peerConnection = new RTCPeerConnection(configuration);
    signalingChannel
}

playVideoFromCamera();