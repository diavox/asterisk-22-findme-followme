# Asterisk 22 Find Me Example

This repository provides an example dialplan demonstrating the **Find Me / Follow Me** feature in **Asterisk 22**.

## 📖 Overview

The **Find Me** feature allows incoming calls to be automatically routed through a list of extensions or external numbers until the call is answered. This is commonly used for mobile extensions, operators, or users who need their calls to follow them between locations.

This example includes:
- Sequential ringing logic using `While()` loops and `Dial()` commands  
- Dynamic `findme_enabled`, `findme_exten[]`, and `findme_ring[]` variables  
- Proper handling of `DIALSTATUS` to determine call flow  
- Optional voicemail fallback when all attempts fail  
- Example of using `UNIQUEID` for tracking and logging

## ⚙️ Features

- Compatible with **Asterisk 22 and later**
- Modular structure for easy integration
- Clear inline comments for educational use
- Simple to adapt for production or lab testing

## 📂 Example Context

```asterisk
[from-internal]
exten => _X.,1,NoOp(Call Entry DNIS=${EXTEN})
 same => n,Verbose(=== Starting Find-Me Logic ===)
 same => n,Dial(PJSIP/6002,20,g)
 same => n,GotoIf($["${DIALSTATUS}"="ANSWER"]?end:call_failed)

 same => n(call_failed),Verbose(Call not answered, proceeding with Find-Me)
 same => n,GotoIf($["${findme_enabled}"="true"]?findme_enabled:voicemail)

 same => n(findme_enabled),Verbose(Executing Find-Me sequence)
 same => n,Set(CTR=0)
 same => n(loop),While($[${CTR} < ${FINDME_COUNT}])
 same => n,Set(NUMBER_TO_DIAL=${FINDME_EXT[${CTR}]})
 same => n,Set(RING_TIME=${FINDME_RING[${CTR}]})
 same => n,Gosub(make-dial,s,1)
 same => n,ExecIf($["${DIALSTATUS}"="ANSWER"]?Goto(end))
 same => n,Set(CTR=$[${CTR} + 1])
 same => n,EndWhile()
 same => n,Goto(voicemail)

 same => n(voicemail),Verbose(Executing voicemail)
 same => n,Playback(vm-intro)
 same => n,Record(/var/spool/asterisk/voicemail/${UNIQUEID}.wav,5,60)
 same => n(end),Hangup()
```

## 🔔 Parallel Ringing (Simultaneous) Example

Dial extension `7000` from an internal phone to trigger the parallel-ringing
Find Me example. This will attempt to ring `6003` and `6004` at the same time
for 30 seconds. If one of the endpoints is not registered or unreachable,
PJSIP will typically ignore that contact and ring the available ones.

Example behavior:
- Call 7000 -> Asterisk executes Dial(PJSIP/6003&PJSIP/6004,30)
- All listed endpoints ring simultaneously
- If any endpoint answers, the call is bridged and the dialplan continues
	(DIALSTATUS will be ANSWER)
- If none answer, the existing Find Me fallback logic and voicemail will
	be used as configured in the dialplan

This example is intended for demonstration and can be expanded by adding
more endpoints to the dial string (separated by `&`) or by inserting
pre-checks to filter only registered contacts if your deployment requires it.

## 🧩 Version Compatibility

- **Tested on:** Asterisk 22.x
- **Requires:** PJSIP channel driver

## 📜 License

This project is open-source and available under the [MIT License](LICENSE).
