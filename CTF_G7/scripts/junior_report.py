#!/usr/bin/env python3
import datetime
import report_utils


print(f"Relatorio NexaByte gerado em {datetime.datetime.utcnow().isoformat()}Z")
report_utils.collect()
