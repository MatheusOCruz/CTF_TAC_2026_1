import { Router, type Request, type Response } from "express";
const router = Router();

// 123,420,2067,6700

/* GET /api/potion/:id
   (caminho aqui é relativo ao ponto onde o router foi montado: "/api") */
router.get("/potion/:id", (req: Request, res: Response) => {
  let id = req.params.id;
  if (id === "123") {
    res.json({ id: req.params.id, status: "ok", message: "note7/note7.gif" });
    return;
  }
  if (id === "420") {
    res.json({ id: req.params.id, status: "ok", message: "apex/apex.html" });
    return;
  }
  if (id === "2067") {
    res.json({ id: req.params.id, status: "ok", message: "ship/ship.html" });
    return;
  }
  if (id === "6700") {
    res.json({ id: req.params.id, status: "ok", message: "ilove/loveMusic.jpg" });
    return;
  }
  else {
    return res.status(404).json({
      id: req.params.id,
      status: "error",
      message: "/potions/potions.html",
    });
  }
});

/* POST /api/extract  — valida a flag enviada pelo front */
router.post("/extract", (req: Request, res: Response) => {
  const flag = String(req.body?.flag ?? "").trim();
  if (flag === "peculiarloot") {
    res.json({ status: "ok", message: "REPO{4.SQU4R3.B4LL}" });
    return;
  }
  res.status(401).json({ status: "rejected", message: "FLAG REJECTED." });
});

router.post("/validate/:id", (req: Request, res: Response) => {
  const reqParam = req.params.id;
  const body = req.body.ans;

  switch (reqParam) {
    case '0.1':
      if (body === 'REPO{Sem1botLooksSo63757465T0day}') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '0.2':
      if (body === 'REPO{R3TR13VE.3XTR4CT.4ND.PR0F1T.0P3R4TI0N}') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '1':
      if (body === 'application/json') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '2':
      if (body === 'apexpredator') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '3':
      if (body === 'duck') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '4':
      if (body === 'REPO{4.SQU4R3.B4LL}') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '5':
      if (body === 'semibot') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '6':
      if (body === 'iloveher') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '7':
      if (body === 'REPO{D0NT.T0UCH.TH3.DUCK}') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '8':
      if (body === 'taxman') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '9':
      if (body === 'verrybigsmile') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '10':
      if (body === 'welcome.sh') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '11':
      if (body === 'sys-check') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    case '12':
      if (body === 'shopkeeper') {
        return res.json({ status: "ok", message: "CORRECT." });
      }
      else {
        return res.status(400).json({
          status: "error",
          message: "WRONG.",
        });
      }
    default:
      return res.status(404).json({
        status: "error",
        message: "Validation not found.",
      });
  }

})

export default router;
